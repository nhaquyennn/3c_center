<style>
.face-attendance-container{
    max-width:700px;
    margin:auto;
    text-align:center;
}

.camera-box{
    position:relative;
    display:inline-block;
}

#video{
    width:100%;
    border-radius:10px;
}

#overlay{
    position:absolute;
    top:0;
    left:0;
    width:100%;
    height:100%;
    pointer-events:none;
}

#guide-text{
    position:absolute;
    top:15px;
    left:50%;
    transform:translateX(-50%);
    background:rgba(0,0,0,.6);
    color:#fff;
    padding:8px 15px;
    border-radius:8px;
    font-size:18px;
}

.attendance-status{
    margin-top:20px;
    font-size:22px;
    font-weight:bold;
}
#toast{
    position: fixed;
    top:20px;
    right:20px;
    background:#00b894;
    color:white;
    padding:15px 20px;
    border-radius:10px;
    font-size:16px;
    display:none;
    z-index:9999;
}
</style>

<div id="main">

    <div class="page-heading">

        <div class="face-attendance-container">

            <h2>Chấm công giảng viên</h2>

            <div class="camera-box">

                <video
                    id="video"
                    autoplay
                    playsinline
                ></video>

                <canvas id="overlay"></canvas>

                <div id="guide-text">
                    Đưa khuôn mặt vào camera
                </div>

            </div>

            <div
                class="attendance-status"
                id="attendance-status"
            >
                Đang khởi động AI...
            </div>

        </div>
<div id="toast"></div>
    </div>

</div>

<script>

const video = document.getElementById("video");
const overlay = document.getElementById("overlay");
const ctx = overlay.getContext("2d");
const statusEl = document.getElementById("attendance-status");

let cameraReady = false;
let isSaving = false;
let lastCheck = {};

// ======================================
// CAMERA
// ======================================
async function startCamera()
{
    try {
        const stream = await navigator.mediaDevices.getUserMedia({
            video: true
        });

        video.srcObject = stream;

        video.onloadedmetadata = () => {
            cameraReady = true;
            statusEl.innerHTML = "📷 Camera sẵn sàng";
        };

    } catch (err) {
        console.log("CAMERA ERROR:", err);
        statusEl.innerHTML = "❌ Không mở được camera";
    }
}

startCamera();

// ======================================
// LOOP AI
// ======================================
setInterval(async () => {

    if (!cameraReady) return;
    if (video.videoWidth === 0) return;

    overlay.width = video.videoWidth;
    overlay.height = video.videoHeight;

    ctx.clearRect(0, 0, overlay.width, overlay.height);

    const tempCanvas = document.createElement("canvas");
    tempCanvas.width = video.videoWidth;
    tempCanvas.height = video.videoHeight;

    const tempCtx = tempCanvas.getContext("2d");
    tempCtx.drawImage(video, 0, 0);

    tempCanvas.toBlob(async (blob) => {

        const formData = new FormData();
        formData.append("image", blob, "face.jpg");

        try {

            const res = await fetch(
                "http://127.0.0.1:8000/recognize",
                {
                    method: "POST",
                    body: formData
                }
            );

            const data = await res.json();

            console.log("🔵 AI RESPONSE:", data); // DEBUG QUAN TRỌNG

            handleResult(data);

        } catch (err) {
            console.log("FETCH ERROR:", err);
            statusEl.innerHTML = "❌ Lỗi AI server";
        }

    }, "image/jpeg");

}, 1500);

// ======================================
// HANDLE RESULT
// ======================================
function handleResult(data)
{
    if (!data) {
        console.log("EMPTY DATA");
        return;
    }

    if (!data.success) {
        console.log("AI FAIL:", data.message);
        return;
    }

    if (!data.face_found) {
        statusEl.innerHTML = "👤 Không thấy khuôn mặt";
        return;
    }

    // DRAW BOX
    if (data.box) {
        ctx.strokeStyle = "#00ff00";
        ctx.lineWidth = 3;

        ctx.strokeRect(
            data.box.left,
            data.box.top,
            data.box.width,
            data.box.height
        );

        ctx.fillStyle = "#00ff00";
        ctx.font = "20px Arial";
        ctx.fillText(
            data.name,
            data.box.left,
            data.box.top - 10
        );
    }

    statusEl.innerHTML = "🔍 Nhận diện: " + data.name;

    // DEBUG USER ID
    console.log("USER_ID:", data.user_id);

    if (!data.user_id) {
        console.log("❌ Không có user_id => không checkin");
        return;
    }

    const now = Date.now();

    if (lastCheck[data.user_id] &&
        now - lastCheck[data.user_id] < 10000
    ) {
        console.log("⛔ Spam blocked");
        return;
    }

    lastCheck[data.user_id] = now;

    saveAttendance(data.user_id);
}

// ======================================
// CHECKIN
// ======================================
function saveAttendance(user_id)
{
    if (isSaving) {
        console.log("⛔ isSaving lock");
        return;
    }

    isSaving = true;

    statusEl.innerHTML = "⏳ Đang check-in...";

    const canvas = document.createElement("canvas");
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;

    const c = canvas.getContext("2d");
    c.drawImage(video, 0, 0);

    canvas.toBlob(async (blob) => {

        const formData = new FormData();

        formData.append("teacher_id", user_id);
        formData.append("image", blob, "attendance.jpg");

        try {

            const res = await fetch(
                "?module=face&action=checkIn",
                {
                    method: "POST",
                    body: formData
                }
            );

            const text = await res.text(); // DEBUG RAW

            console.log("🟡 RAW RESPONSE:", text);

            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                console.log("❌ JSON ERROR");
                statusEl.innerHTML = "❌ Server trả sai JSON";
                return;
            }

            console.log("🟢 CHECKIN RESULT:", data);

            // SUCCESS
            if (data.success) {

                statusEl.innerHTML =
                    "✔ " + (data.teacher_name ?? "Đã check-in");

                showToast("✔ " + (data.status ?? "OK"));

            } else {

                statusEl.innerHTML = "⚠ " + data.message;
                showToast("⚠ " + data.message);
            }

        } catch (err) {
            console.log("CHECKIN ERROR:", err);
            statusEl.innerHTML = "❌ Lỗi server check-in";
        }

    }, "image/jpeg");

    setTimeout(() => {
        isSaving = false;
    }, 8000);
}

// ======================================
// TOAST
// ======================================
function showToast(msg)
{
    const toast = document.getElementById("toast");

    toast.innerHTML = msg;
    toast.style.display = "block";

    setTimeout(() => {
        toast.style.display = "none";
    }, 3000);
}

</script>