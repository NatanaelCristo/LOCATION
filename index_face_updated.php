<?php
// Deteksi cabang berdasarkan HTTP referer
$cabang = 'jakarta'; // default
if (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
    if (strpos($referer, 'tangerang.dstmobil.com') !== false) {
        $cabang = 'tangerang';
    } elseif (strpos($referer, 'jakarta.dstmobil.com') !== false) {
        $cabang = 'jakarta';
    }
}

// Jika ada parameter cabang di URL, gunakan itu
if (isset($_GET['cabang'])) {
    $cabang = strtolower($_GET['cabang']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Face Detection - <?php echo ucfirst($cabang); ?></title>
  <script defer src="face-api.min.js"></script>
  <style>
    body {
      font-family: Arial, sans-serif;
      text-align: center;
      margin: 0;
      padding: 0;
      background: #f9f9f9;
    }
    #video-container {
      position: relative;
      width: 600px;
      height: 450px;
      margin: 20px auto;
    }
    video {
      width: 100%;
      height: 100%;
      border: 3px solid #444;
      border-radius: 10px;
      display: block;
    }
    canvas {
      position: absolute;
      top: 0;
      left: 0;
    }
    #debug-log {
      display: none; /* Disembunyikan */
      margin: 20px auto;
      padding: 10px;
      width: 90%;
      max-width: 700px;
      background: #f0f0f0;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-family: monospace;
    }
    #log-output {
      white-space: pre-wrap;
      font-size: 0.9em;
      text-align: left;
      max-height: 300px;
      overflow-y: auto;
    }
    .branch-info {
      background: #e3f2fd;
      padding: 10px;
      margin: 10px auto;
      border-radius: 5px;
      max-width: 600px;
      color: #1976d2;
    }
  </style>
</head>
<body>
  <h2>Deteksi Wajah Pemilik Kendaraan</h2>
  <div class="branch-info">
    <strong>Cabang: <?php echo ucfirst($cabang); ?></strong>
  </div>

  <div id="video-container">
    <video id="video" width="600" height="450" autoplay muted></video>
  </div>

  <div id="debug-log">
    <strong>Debug Log:</strong>
    <pre id="log-output"></pre>
  </div>

  <script>
    const video = document.getElementById("video");
    const logOutput = document.getElementById("log-output");
    const wajahTerdeteksi = new Set();
    const nikKeNama = {}; // Map NIK ke Nama
    const cabang = "<?php echo $cabang; ?>"; // Ambil cabang dari PHP

    function log(message) {
      const now = new Date().toLocaleTimeString();
      logOutput.textContent += `[${now}] ${message}\n`;
      logOutput.scrollTop = logOutput.scrollHeight;
    }

    document.addEventListener("DOMContentLoaded", async () => {
      await Promise.all([
        faceapi.nets.ssdMobilenetv1.loadFromUri("/FACE/models"),
        faceapi.nets.faceRecognitionNet.loadFromUri("/FACE/models"),
        faceapi.nets.faceLandmark68Net.loadFromUri("/FACE/models"),
      ]);
      log("✅ Model face-api berhasil dimuat.");
      log("📍 Cabang terdeteksi: " + cabang);

      startWebcam();

      async function startWebcam() {
        try {
          const stream = await navigator.mediaDevices.getUserMedia({ video: true });
          video.srcObject = stream;
          log("📷 Kamera aktif dan dimulai.");
        } catch (error) {
          log("❌ Gagal mengakses kamera: " + error);
        }
      }

      async function getLabeledFaceDescriptions() {
        const response = await fetch("labels.php");
        const data = await response.json(); // hasil: [{file: '1', nama: 'Ronaldo'}, ...]

        return Promise.all(
          data.map(async (item) => {
            try {
              const imgPath = `labels/${item.file}.jpg`;
              log("Memuat gambar: " + imgPath);
              const img = await faceapi.fetchImage(imgPath);
              const detection = await faceapi
                .detectSingleFace(img)
                .withFaceLandmarks()
                .withFaceDescriptor();

              if (!detection) {
                log("❌ Tidak ada wajah pada gambar: " + imgPath);
                return null;
              }

              nikKeNama[item.file] = item.nama; // Simpan NIK → Nama

              log("✔️ Label dikenali: " + item.nama);
              return new faceapi.LabeledFaceDescriptors(item.file, [detection.descriptor]);
            } catch (err) {
              log("⚠️ Gagal memproses: " + item.file + " (" + err + ")");
              return null;
            }
          })
        ).then(results => results.filter(r => r !== null));
      }

      video.addEventListener("play", async () => {
        log("🎯 Mulai proses deteksi wajah...");
        const labeledFaceDescriptors = await getLabeledFaceDescriptions();
        const faceMatcher = new faceapi.FaceMatcher(labeledFaceDescriptors);

        const canvas = faceapi.createCanvasFromMedia(video);
        document.getElementById("video-container").appendChild(canvas);

        const displaySize = { width: video.width, height: video.height };
        faceapi.matchDimensions(canvas, displaySize);

        setInterval(async () => {
          const detections = await faceapi
            .detectAllFaces(video)
            .withFaceLandmarks()
            .withFaceDescriptors();

          const resizedDetections = faceapi.resizeResults(detections, displaySize);
          canvas.getContext("2d").clearRect(0, 0, canvas.width, canvas.height);

          const results = resizedDetections.map((d) =>
            faceMatcher.findBestMatch(d.descriptor)
          );

          results.forEach((result, i) => {
            const box = resizedDetections[i].detection.box;
            const nik = result.label;
            const nama = nikKeNama[nik] || nik;
            const finalLabel = nik === "unknown" ? "Tidak dikenal" : nama;

            const drawBox = new faceapi.draw.DrawBox(box, {
              label: finalLabel,
            });
            drawBox.draw(canvas);

            if (nik !== "unknown" && !wajahTerdeteksi.has(nik)) {
              wajahTerdeteksi.add(nik);
              log("🔍 Wajah dikenali sebagai NIK: " + nik + " (" + nama + ")");

              fetch('kirim_servis.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ nik })
              })
              .then(res => res.json())
              .then(data => {
                if (data.status === "success") {
                  log("✅ Data servis berhasil dikirim:");
                  log("   Pemilik: " + data.nama);
                  log("   No. Rangka: " + data.no_rangka);
                  log("   Tanggal: " + data.tanggal_simpan);

                  const daftarCabang = {
                    jakarta: "https://jakarta.dstmobil.com/input_servis",
                    tangerang: "https://tangerang.dstmobil.com/input_servis"
                  };

                  const targetURL = daftarCabang[cabang] || daftarCabang["jakarta"];

                  log("📍 Cabang yang terdeteksi: " + cabang);
                  log("🔗 Redirect ke: " + targetURL);

                  const form = document.createElement("form");
                  form.method = "POST";
                  form.action = targetURL;

                  const input = (name, value) => {
                    const i = document.createElement("input");
                    i.type = "hidden";
                    i.name = name;
                    i.value = value;
                    return i;
                  };

                  form.appendChild(input("id_pemilik", data.id_pemilik));
                  form.appendChild(input("nama_pemilik", data.nama));
                  form.appendChild(input("no_rangka", data.no_rangka));
                  form.appendChild(input("model_mobil", data.model_mobil));
                  form.appendChild(input("plat_mobil", data.plat_mobil));
                  form.appendChild(input("tanggal_servis", data.tanggal_simpan.slice(0, 10)));
                  form.appendChild(input("mode", "prefill"));

                  document.body.appendChild(form);
                  form.submit();
                } else {
                  log("❌ Gagal menyimpan data servis: " + data.message);
                }
              })
              .catch(err => {
                log("❌ Error kirim data: " + err);
              });
            }
          });
        }, 1000);
      });
    });
  </script>
</body>
</html>