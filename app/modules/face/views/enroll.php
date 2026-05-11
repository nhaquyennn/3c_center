<style>
.face-page{
    padding:24px;
}

.face-card{
    background:#fff;
    border-radius:20px;
    padding:24px;
    max-width:900px;
    margin:auto;
    box-shadow:0 10px 30px rgba(0,0,0,.05);
}

.face-header h2{
    font-size:24px;
    margin-bottom:6px;
}

.face-header p{
    color:#777;
    margin-bottom:24px;
}

.camera-wrapper{
    width:100%;
    border-radius:18px;
    overflow:hidden;
    background:#111;
    margin-top:20px;
    position:relative;
}

#video{
    width:100%;
    display:block;
}

#canvas{
    display:none;
}

.capture-progress{
    margin-top:20px;
}

.progress-bar{
    height:12px;
    background:#eee;
    border-radius:999px;
    overflow:hidden;
}

.progress-fill{
    width:0%;
    height:100%;
    background:#1D9E75;
    transition:.3s;
}

#capture-count{
    margin-top:8px;
    font-size:13px;
    color:#666;
}

.face-actions{
    margin-top:24px;
    display:flex;
    gap:12px;
}

#face-status{
    margin-top:18px;
    font-weight:600;
    color:#444;
    min-height:30px;
}

.face-guide{
    margin-top:15px;
    background:#fff3cd;
    padding:12px;
    border-radius:12px;
    color:#856404;
    font-size:14px;
}

.pose-box{
    position:absolute;
    top:15px;
    left:50%;
    transform:translateX(-50%);
    background:rgba(0,0,0,.6);
    color:white;
    padding:10px 18px;
    border-radius:12px;
    font-size:18px;
    z-index:10;
}

@media(max-width:768px){

    .face-actions{
        flex-direction:column;
    }

}
</style>

<div id="main">

<header class="mb-3">
    <a href="#" class="burger-btn d-block d-xl-none">
        <i class="bi bi-justify fs-3"></i>
    </a>
</header>

<div class="page-heading">

<div class="face-page">

<div class="face-card">

    <div class="face-header">

        <h2>Đăng ký khuôn mặt AI</h2>

        <p>
            Thu thập nhiều góc mặt để tăng độ chính xác nhận diện
        </p>

    </div>

    <!-- USER -->
    <div class="form-group">

        <label>Chọn người dùng</label>

        <select
            id="user_id"
            class="form-select"
        >

            <option value="">
                -- Chọn --
            </option>

            <?php foreach($users as $u): ?>

                <option value="<?= $u['user_id'] ?>">

                    <?= $u['name'] ?>
                    (<?= $u['role'] ?>)

                </option>

            <?php endforeach; ?>

        </select>

    </div>

    <!-- GUIDE -->
    <div class="face-guide">

        ⚠ Không đeo khẩu trang •
        Đủ ánh sáng •
        Đưa mặt gần camera •
        Chỉ 1 khuôn mặt trong khung hình

    </div>

    <!-- CAMERA -->
    <div class="camera-wrapper">

        <div
            class="pose-box"
            id="pose-box"
        >
            Chưa bắt đầu
        </div>

        <video
            id="video"
            autoplay
            playsinline>
        </video>

        <canvas id="canvas"></canvas>

    </div>

    <!-- PROGRESS -->
    <div class="capture-progress">

        <div class="progress-bar">

            <div
                id="progress-fill"
                class="progress-fill">
            </div>

        </div>

        <div id="capture-count">

            0 / 10 ảnh

        </div>

    </div>

    <!-- ACTIONS -->
    <div class="face-actions">

        <button
            class="btn btn-primary"
            onclick="startCamera()"
        >
            Bật camera
        </button>

        <button
            id="enroll-btn"
            class="btn btn-success"
            onclick="startEnroll()"
        >
            Bắt đầu đăng ký
        </button>

    </div>

    <!-- STATUS -->
    <div id="face-status"></div>

</div>

</div>

</div>

</div>

<script>

let video =
    document.getElementById("video");

let canvas =
    document.getElementById("canvas");

let stream = null;

let collected = 0;

let enrolling = false;

// ======================================
// 10 GÓC MẶT
// ======================================

const poses = [

    "Nhìn thẳng",

    "Quay trái nhẹ",

    "Quay phải nhẹ",

    "Cúi xuống nhẹ",

    "Ngẩng lên nhẹ",

    "Lại gần camera",

    "Ra xa camera",

    "Nghiêng trái",

    "Nghiêng phải",

    "Biểu cảm tự nhiên"
];

const total = poses.length;

// ======================================
// CAMERA
// ======================================

async function startCamera()
{
    try {

        stream =
            await navigator
            .mediaDevices
            .getUserMedia({

                video: {

                    width: 1280,

                    height: 720,

                    facingMode: "user"
                }

            });

        video.srcObject = stream;

        setStatus(
            "📷 Camera sẵn sàng"
        );

    } catch(err) {

        console.log(err);

        setStatus(
            "❌ Không mở được camera"
        );
    }
}

// ======================================
// START ENROLL
// ======================================

async function startEnroll()
{
    if(enrolling)
    {
        return;
    }

    const user_id =
        document.getElementById(
            "user_id"
        ).value;

    if(!user_id)
    {
        alert("Chọn người dùng");
        return;
    }

    if(!stream)
    {
        alert("Bật camera trước");
        return;
    }

    enrolling = true;

    collected = 0;

    updateProgress();

    document.getElementById(
        "enroll-btn"
    ).disabled = true;

    nextStep();
}

// ======================================
// NEXT STEP
// ======================================

async function nextStep()
{
    if(collected >= total)
    {
        enrolling = false;

        document.getElementById(
            "enroll-btn"
        ).disabled = false;

        setStatus(
            "🎉 Đăng ký hoàn tất"
        );

        document.getElementById(
            "pose-box"
        ).innerHTML =
            "Hoàn tất";

        return;
    }

    let pose =
        poses[collected];

    let countdown = 3;

    document.getElementById(
        "pose-box"
    ).innerHTML = pose;

    const timer =
        setInterval(async()=>{

            setStatus(

                "[" + (collected + 1)
                + "/" + total + "] "

                + pose

                + " - "

                + countdown

            );

            countdown--;

            if(countdown < 0)
            {
                clearInterval(timer);

                let success =
                    await captureFace();

                if(success)
                {
                    collected++;

                    updateProgress();

                    setTimeout(()=>{

                        nextStep();

                    }, 1000);
                }
                else
                {
                    setStatus(
                        "❌ Chụp thất bại"
                    );

                    setTimeout(()=>{

                        nextStep();

                    }, 1500);
                }
            }

        },1000);
}

// ======================================
// CAPTURE
// ======================================

async function captureFace()
{
    try {

        if(video.videoWidth < 300)
        {
            setStatus(
                "❌ Camera quá mờ"
            );

            return false;
        }

        const user_id =
            document.getElementById(
                "user_id"
            ).value;

        const ctx =
            canvas.getContext("2d");

        canvas.width =
            video.videoWidth;

        canvas.height =
            video.videoHeight;

        ctx.drawImage(
            video,
            0,
            0,
            canvas.width,
            canvas.height
        );

        const blob =
            await new Promise(resolve => {

                canvas.toBlob(

                    resolve,

                    "image/jpeg",

                    0.95

                );

            });

        const formData =
            new FormData();

        formData.append(
            "user_id",
            user_id
        );

        formData.append(
            "image",
            blob,
            "face.jpg"
        );

        const res =
            await fetch(

                "http://127.0.0.1:8000/enroll",

                {
                    method: "POST",
                    body: formData
                }

            );

        const data =
            await res.json();

        console.log(data);

        if(data.success)
        {
            setStatus(

                "✅ Đã lưu ảnh "
                + (collected + 1)

            );

            return true;
        }
        else
        {
            setStatus(
                "❌ "
                + data.message
            );

            return false;
        }

    } catch(err) {

        console.log(err);

        setStatus(
            "❌ Lỗi AI server"
        );

        return false;
    }
}

// ======================================
// UI
// ======================================

function updateProgress()
{
    const percent =
        (collected / total) * 100;

    document.getElementById(
        "progress-fill"
    ).style.width =
        percent + "%";

    document.getElementById(
        "capture-count"
    ).innerHTML =

        collected
        + " / "
        + total
        + " ảnh";
}

function setStatus(msg)
{
    document.getElementById(
        "face-status"
    ).innerHTML = msg;
}

</script>