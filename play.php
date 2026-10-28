<?php
$serie_id = $_GET['serie_id'] ?? 0;
$season_id = $_GET['season_id'] ?? 0;
$episode_id = $_GET['episode_id'] ?? 0;
$api_url = "https://server-hi-speed-iran.info/api/season/by/serie/{$serie_id}/4F5A9C3D9A86FA54EACEDDD635185/";
// دریافت داده‌ها از API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);
curl_close($ch);
$seasons = json_decode($response, true);
$selected_season = null;
foreach ($seasons as $season) {
    if ($season['id'] == $season_id) {
        $selected_season = $season;
        break;
    }
}
if (!$selected_season) {
    die("فصل مورد نظر یافت نشد.");
}
$selected_episode = null;
foreach ($selected_season['episodes'] as $episode) {
    if ($episode['id'] == $episode_id) {
        $selected_episode = $episode;
        break;
    }
}
if (!$selected_episode) {
    die("قسمت مورد نظر یافت نشد.");
}
?>
<!DOCTYPE html>
<html lang="fa" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>وبیرو - پلیر سریال</title>
    <link rel="stylesheet" href="assets/app.css?v=<?= time() ?>">
    <script src="assets/jquery.js?v=<?= time() ?>"></script>
</head>
<body>
    <div id="loading-spinner" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 50; display: none;">
        <svg class="rp-w-12 rp-animate-rotate" viewBox="25 25 50 50"><circle class="rp-animate-dash rp-fill-none rp-stroke-primary" cx="50" cy="50" r="20" style="stroke-width: 4; stroke-dasharray: 1, 200; stroke-dashoffset: 0; stroke-linecap: round;"></circle></svg>
    </div>
    <div class="player-container">
        <div class="season-title">
            <season><?= htmlspecialchars($selected_season['title']) ?></season> -
            <episode id="nextEpisodeTitle"><?= htmlspecialchars($selected_episode['title']) ?></episode>
        </div>
        <!-- <img id="poster" class="poster" src="<?= $selected_episode['sources'][0]['url'] ?>" alt="پیش‌نمایش"> -->
        <div class="play-button" id="play-button">▶️</div>
        <div class="overlay" id="overlay">
            <div class="controls">
                <button id="pause-resume">
                    <svg viewBox="0 0 24 24" class="rp-fill-white">
                        <path d="M7 6.13402V17.866C7 18.7606 8.02908 19.3042 8.82159 18.8172L18.45 12.9512C19.1833 12.5096 19.1833 11.4904 18.45 11.0374L8.82159 5.18278C8.02908 4.69583 7 5.2394 7 6.13402Z"></path>
                    </svg>
                </button>
                <div class="seek-bar" id="seek-bar">
                    <div class="progress" id="progress"></div>
                </div>
                <button id="mute-btn">
                    <svg viewBox="0 0 24 24" class="rp-fill-white rp-w-[22px] rp-h-[22px] rp-transition-all">
                        <path d="M19.45 4.71001C19.2657 4.51773 19.0125 4.40655 18.7462 4.40092C18.4799 4.39529 18.2223 4.49569 18.03 4.68001C17.8377 4.86434 17.7265 5.1175 17.7209 5.3838C17.7153 5.6501 17.8157 5.90773 18 6.10001C19.5217 7.68329 20.3715 9.79404 20.3715 11.99C20.3715 14.186 19.5217 16.2967 18 17.88C17.8618 18.021 17.7684 18.1997 17.7316 18.3937C17.6948 18.5877 17.7163 18.7882 17.7933 18.97C17.8703 19.1518 17.9994 19.3068 18.1643 19.4153C18.3292 19.5239 18.5226 19.5812 18.72 19.58C18.8547 19.5795 18.9879 19.5518 19.1116 19.4985C19.2354 19.4452 19.347 19.3675 19.44 19.27C21.3198 17.3126 22.3696 14.7039 22.3696 11.99C22.3696 9.27611 21.3198 6.66741 19.44 4.71001H19.45ZM11.38 4.79002C11.1962 4.79127 11.0163 4.84317 10.86 4.94002L5.88 8.00002H3.38C2.84957 8.00002 2.34086 8.21073 1.96579 8.5858C1.59072 8.96088 1.38 9.46958 1.38 10V14C1.38 14.5304 1.59072 15.0392 1.96579 15.4142C2.34086 15.7893 2.84957 16 3.38 16H5.9L10.9 19.06C11.0573 19.1534 11.2371 19.2018 11.42 19.2C11.6852 19.2 11.9396 19.0947 12.1271 18.9071C12.3146 18.7196 12.42 18.4652 12.42 18.2V5.78002C12.4188 5.6461 12.3907 5.5138 12.3373 5.39096C12.284 5.26812 12.2065 5.15725 12.1095 5.06491C12.0125 4.97258 11.898 4.90067 11.7727 4.85345C11.6473 4.80622 11.5138 4.78465 11.38 4.79002ZM15.9895 7.29371C16.2538 7.30683 16.5021 7.42417 16.68 7.62001L16.71 7.60001C17.8006 8.7941 18.4053 10.3528 18.4053 11.97C18.4053 13.5872 17.8006 15.1459 16.71 16.34C16.6165 16.4436 16.5024 16.5265 16.3749 16.5833C16.2475 16.6401 16.1096 16.6697 15.97 16.67C15.7222 16.6694 15.4834 16.5767 15.3 16.41C15.1042 16.2321 14.9868 15.9838 14.9737 15.7195C14.9606 15.4552 15.0528 15.1965 15.23 15C15.9722 14.1766 16.3852 13.1086 16.39 12C16.3845 10.8879 15.9636 9.81791 15.21 9.00001C15.1154 8.90258 15.0417 8.78691 14.9933 8.66005C14.9448 8.53318 14.9228 8.39779 14.9284 8.26211C14.934 8.12644 14.9672 7.99333 15.0259 7.8709C15.0846 7.74848 15.1677 7.73929 15.27 7.55001C15.4665 7.37275 15.7252 7.28059 15.9895 7.29371Z"></path>
                    </svg>
                </button>
                <button id="prev-episode">
                    <svg viewBox="0 0 24 24" class="rotate-90 rp-fill-white rp-w-5 rp-h-5 rp-transition-all">
                        <path d="M20 3.77808V20.2781C20 20.477 19.921 20.6678 19.7803 20.8084C19.6397 20.9491 19.4489 21.0281 19.25 21.0281C19.0511 21.0281 18.8603 20.9491 18.7197 20.8084C18.579 20.6678 18.5 20.477 18.5 20.2781V13.7878L7.29031 20.7984C7.06456 20.9401 6.80504 21.0189 6.5386 21.0266C6.27217 21.0343 6.00852 20.9707 5.77494 20.8423C5.54137 20.7139 5.34636 20.5254 5.21011 20.2963C5.07386 20.0672 5.00132 19.8059 5 19.5393V4.51683C5.00132 4.25029 5.07386 3.98894 5.21011 3.75985C5.34636 3.53076 5.54137 3.34226 5.77494 3.21385C6.00852 3.08545 6.27217 3.02181 6.5386 3.02953C6.80504 3.03725 7.06456 3.11605 7.29031 3.25776L18.5 10.2684V3.77808C18.5 3.57916 18.579 3.3884 18.7197 3.24775C18.8603 3.10709 19.0511 3.02808 19.25 3.02808C19.4489 3.02808 19.6397 3.10709 19.7803 3.24775C19.921 3.3884 20 3.57916 20 3.77808Z"></path>
                    </svg>
                    <span>قسمت قبلی</span>
                </button>
                <button id="next-episode">
                    <span>قسمت بعدی</span>
                    <svg viewBox="0 0 24 24" class="rp-fill-white rp-w-5 rp-h-5 rp-transition-all">
                        <path d="M20 3.77808V20.2781C20 20.477 19.921 20.6678 19.7803 20.8084C19.6397 20.9491 19.4489 21.0281 19.25 21.0281C19.0511 21.0281 18.8603 20.9491 18.7197 20.8084C18.579 20.6678 18.5 20.477 18.5 20.2781V13.7878L7.29031 20.7984C7.06456 20.9401 6.80504 21.0189 6.5386 21.0266C6.27217 21.0343 6.00852 20.9707 5.77494 20.8423C5.54137 20.7139 5.34636 20.5254 5.21011 20.2963C5.07386 20.0672 5.00132 19.8059 5 19.5393V4.51683C5.00132 4.25029 5.07386 3.98894 5.21011 3.75985C5.34636 3.53076 5.54137 3.34226 5.77494 3.21385C6.00852 3.08545 6.27217 3.02181 6.5386 3.02953C6.80504 3.03725 7.06456 3.11605 7.29031 3.25776L18.5 10.2684V3.77808C18.5 3.57916 18.579 3.3884 18.7197 3.24775C18.8603 3.10709 19.0511 3.02808 19.25 3.02808C19.4489 3.02808 19.6397 3.10709 19.7803 3.24775C19.921 3.3884 20 3.57916 20 3.77808Z"></path>
                    </svg>
                </button>
                
                <!-- دکمه کیفیت -->
                <button id="quality-btn">
                    <span id="current-quality-label">کیفیت</span>
                    <svg viewBox="0 0 24 24" class="rp-fill-white rp-w-[22px] rp-h-[22px] rp-transition-all">
                        <path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"></path>
                    </svg>
                </button>
                
                <button id="fullscreen-btn">
                    <svg viewBox="0 0 24 24" class="rp-fill-white rp-w-[22px] rp-h-[22px] rp-transition-all">
                        <path d="M5.5 2.5H9.5C9.76522 2.5 10.0196 2.60536 10.2071 2.79289C10.3946 2.98043 10.5 3.23478 10.5 3.5C10.5 3.76522 10.3946 4.01957 10.2071 4.20711C10.0196 4.39464 9.76522 4.5 9.5 4.5H5.5C5.23478 4.5 4.98043 4.60536 4.79289 4.79289C4.60536 4.98043 4.5 5.23478 4.5 5.5V9.5C4.5 9.76522 4.39464 10.0196 4.20711 10.2071C4.01957 10.3946 3.76522 10.5 3.5 10.5C3.23478 10.5 2.98043 10.3946 2.79289 10.2071C2.60536 10.0196 2.5 9.76522 2.5 9.5V5.5C2.5 4.70435 2.81607 3.94129 3.37868 3.37868C3.94129 2.81607 4.70435 2.5 5.5 2.5ZM5.5 19.5H9.5C9.76522 19.5 10.0196 19.6054 10.2071 19.7929C10.3946 19.9804 10.5 20.2348 10.5 20.5C10.5 20.7652 10.3946 21.0196 10.2071 21.2071C10.0196 21.3946 9.76522 21.5 9.5 21.5H5.5C4.70435 21.5 3.94129 21.1839 3.37868 20.6213C2.81607 20.0587 2.5 19.2957 2.5 18.5V14.5C2.5 14.2348 2.60536 13.9804 2.79289 13.7929C2.98043 13.6054 3.23478 13.5 3.5 13.5C3.76522 13.5 4.01957 13.6054 4.20711 13.7929C4.39464 13.9804 4.5 14.2348 4.5 14.5V18.5C4.5 18.7652 4.60536 19.0196 4.79289 19.2071C4.98043 19.3946 5.23478 19.5 5.5 19.5ZM20.5 13.5C20.2348 13.5 19.9804 13.6054 19.7929 13.7929C19.6054 13.9804 19.5 14.2348 19.5 14.5V18.5C19.5 18.7652 19.3946 19.0196 19.2071 19.2071C19.0196 19.3946 18.7652 19.5 18.5 19.5H14.5C14.2348 19.5 13.9804 19.6054 13.7929 19.7929C13.6054 19.9804 13.5 20.2348 13.5 20.5C13.5 20.7652 13.6054 21.0196 13.7929 21.2071C13.9804 21.3946 14.2348 21.5 14.5 21.5H18.5C19.2957 21.5 20.0587 21.1839 20.6213 20.6213C21.1839 20.0587 21.5 19.2957 21.5 18.5V14.5C21.5 14.2348 21.3946 13.9804 21.2071 13.7929C21.0196 13.6054 20.7652 13.5 20.5 13.5ZM14.5 2.5H18.5C19.2957 2.5 20.0587 2.81607 20.6213 3.37868C21.1839 3.94129 21.5 4.70435 21.5 5.5V9.5C21.5 9.76522 21.3946 10.0196 21.2071 10.2071C21.0196 10.3946 20.7652 10.5 20.5 10.5C20.2348 10.5 19.9804 10.3946 19.7929 10.2071C19.6054 10.0196 19.5 9.76522 19.5 9.5V5.5C19.5 5.23478 19.3946 4.98043 19.2071 4.79289C19.0196 4.60536 18.7652 4.5 18.5 4.5H14.5C14.2348 4.5 13.9804 4.39464 13.7929 4.20711C13.6054 4.01957 13.5 3.76522 13.5 3.5C13.5 3.23478 13.6054 2.98043 13.7929 2.79289C13.9804 2.60536 14.2348 2.5 14.5 2.5Z"></path>
                    </svg>
                </button>

                <!-- منوی کیفیت -->
                <div class="quality-menu" id="quality-menu">
                    <!-- گزینه‌ها توسط جاوااسکریپت پر می‌شوند -->
                </div>
            </div>
        </div>
        <playIcon>
            <svg viewBox="0 0 24 24" class="rp-fill-white">
                <path d="M7 6.13402V17.866C7 18.7606 8.02908 19.3042 8.82159 18.8172L18.45 12.9512C19.1833 12.5096 19.1833 11.4904 18.45 11.0374L8.82159 5.18278C8.02908 4.69583 7 5.2394 7 6.13402Z"></path>
            </svg>
        </playIcon>
        <video id="video-player" style="display: none;"></video>
    </div>

    <script>
        jQuery(document).ready(function($) {
            let videoElement = document.getElementById('video-player');
            let poster = document.getElementById('poster');
            let playButton = document.getElementById('play-button');
            let pauseResumeBtn = document.getElementById('pause-resume');
            let seekBar = document.getElementById('seek-bar');
            let progress = document.getElementById('progress');
            let muteBtn = document.getElementById('mute-btn');
            let fullscreenBtn = document.getElementById('fullscreen-btn');
            let prevEpisodeBtn = document.getElementById('prev-episode');
            let nextEpisodeBtn = document.getElementById('next-episode');
            let overlay = document.getElementById('overlay');
            let nextEpisodeTitle = document.getElementById('nextEpisodeTitle');
            let qualityBtn = document.getElementById('quality-btn');
            let qualityMenu = document.getElementById('quality-menu');
            let currentQualityLabel = document.getElementById('current-quality-label');
            
            let episodes = <?= json_encode($selected_season['episodes']) ?>;
            let currentEpisodeIndex = <?= array_search($selected_episode, $selected_season['episodes']) ?>;
            
            let sources = [];
            let currentSourceIndex = 0;
            let isAutoSwitching = false; // برای جلوگیری از لوپ هنگام خطا

            // تابع استخراج کیفیت از URL
            function getQualityLabel(source) {
                if (source.quality) {
                    return source.quality;
                }
                // استخراج از URL
                let url = source.url;
                let label = "کیفیت معمولی";
                
                // الگوهای رایج
                if (url.includes('1080p')) label = "1080p";
                else if (url.includes('720p')) label = "720p";
                else if (url.includes('480p')) label = "480p";
                else if (url.includes('4K')) label = "4K";
                else {
                    // تلاش برای پیدا کردن رزولوشن در نام فایل
                    let match = url.match(/(\d{3,4})p/);
                    if (match) label = match[0] + "p";
                }
                return label;
            }

            // تابع ساخت منوی کیفیت
            function renderQualityMenu() {
                $('#quality-menu').empty();
                sources.forEach((source, index) => {
                    let label = getQualityLabel(source);
                    let div = $('<div class="quality-option"></div>').text(label);
                    if (index === currentSourceIndex) {
                        div.addClass('active');
                    }
                    div.on('click', function() {
                        changeQuality(index);
                    });
                    $('#quality-menu').append(div);
                });
                $('#current-quality-label').text(getQualityLabel(sources[currentSourceIndex]));
            }

            // تابع تغییر کیفیت
            function changeQuality(index) {
                if (index < 0 || index >= sources.length) return;
                
                let currentTime = videoElement.currentTime;
                let isPaused = videoElement.paused;
                
                currentSourceIndex = index;
                renderQualityMenu();
                
                videoElement.src = "https://webiro.ir/stream/api/proxy.php?url=" + sources[index].url;
                videoElement.currentTime = currentTime;
                
                if (!isPaused) {
                    videoElement.play();
                }
                
                // ریست کردن آیکون پخش/توقف
                updatePlayPauseIcon(!isPaused);
            }

            // تابع لود ویدیو با هندل کردن خطا
            function loadVideoWithFallback(sourceIndex) {
                if (sourceIndex >= sources.length) {
                    // تمام کیفیت‌ها خطا دادند
                    videoElement.src = "https://webiro.ir/stream/assets/error.mp4";
                    videoElement.load();
                    videoElement.play();
                    return;
                }

                isAutoSwitching = true;
                currentSourceIndex = sourceIndex;
                renderQualityMenu();
                
                videoElement.src = "https://webiro.ir/stream/api/proxy.php?url=" + sources[sourceIndex].url;
                videoElement.load();
                videoElement.play().catch(e => {
                    console.log("Play promise rejected (waiting for load):", e);
                });
            }

            // هندل کردن خطای ویدیو
            videoElement.addEventListener('error', function(e) {
                if (isAutoSwitching && currentSourceIndex < sources.length - 1) {
                    console.log("خطا در کیفیت فعلی، تلاش برای کیفیت بعدی...");
                    loadVideoWithFallback(currentSourceIndex + 1);
                } else if (isAutoSwitching) {
                    console.log("تمام کیفیت‌ها خطا دادند.");
                    videoElement.src = "https://webiro.ir/stream/assets/error.mp4";
                    videoElement.load();
                    videoElement.play();
                    isAutoSwitching = false;
                }
            });

            // وقتی ویدیو با موفقیت شروع به پخش کرد، فلگ را خاموش کن
            videoElement.addEventListener('playing', function() {
                isAutoSwitching = false;
            });

            // بستن منو با کلیک بیرون
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#quality-btn').length && !$(e.target).closest('#quality-menu').length) {
                    $('#quality-menu').hide();
                }
            });

            $('#quality-btn').click(function(e) {
                e.stopPropagation();
                $('#quality-menu').toggle();
            });

            function updateSeekBar() {
                if (!isNaN(videoElement.duration)) {
                    let percentage = (videoElement.currentTime / videoElement.duration) * 100;
                    progress.style.width = percentage + '%';
                }
            }

            function updatePlayPauseIcon(isPlaying) {
                if (isPlaying) {
                    pauseResumeBtn.innerHTML = '<svg viewBox="0 0 24 24" class="rp-fill-white rp-w-[22px] rp-h-[22px] rp-transition-all"><path d="M7.5 4.51001H9.5C10.0523 4.51001 10.5 4.95772 10.5 5.51001V18.51C10.5 19.0623 10.0523 19.51 9.5 19.51H7.5C6.94772 19.51 6.5 19.0623 6.5 18.51V5.51001C6.5 4.95772 6.94772 4.51001 7.5 4.51001ZM14.5 4.51001H16.5C17.0523 4.51001 17.5 4.95772 17.5 5.51001V18.51C17.5 19.0623 17.0523 19.51 16.5 19.51H14.5C13.9477 19.51 13.5 19.0623 13.5 18.51V5.51001C13.5 4.95772 13.9477 4.51001 14.5 4.51001Z"></path></svg>';
                } else {
                    pauseResumeBtn.innerHTML = '<svg viewBox="0 0 24 24" class="rp-fill-white"><path d="M7 6.13402V17.866C7 18.7606 8.02908 19.3042 8.82159 18.8172L18.45 12.9512C19.1833 12.5096 19.1833 11.4904 18.45 11.0374L8.82159 5.18278C8.02908 4.69583 7 5.2394 7 6.13402Z"></path></svg>';
                }
            }

            playButton.addEventListener('click', function() {
                $('playIcon').hide();
                poster.style.display = 'none';
                videoElement.style.display = 'block';
                document.body.appendChild(videoElement);
                videoElement.play();
                playButton.style.display = 'none';
            });

            pauseResumeBtn.addEventListener('click', function() {
                if (videoElement.paused) {
                    videoElement.play();
                    updatePlayPauseIcon(true);
                } else {
                    videoElement.pause();
                    updatePlayPauseIcon(false);
                }
            });

            videoElement.addEventListener('click', function() {
                if (videoElement.paused) {
                    videoElement.play();
                    updatePlayPauseIcon(true);
                } else {
                    videoElement.pause();
                    updatePlayPauseIcon(false);
                }
            });

            muteBtn.addEventListener('click', function() {
                videoElement.muted = !videoElement.muted;
                muteBtn.innerHTML = videoElement.muted ? '<svg viewBox="0 0 24 24" class="rp-fill-white rp-w-[22px] rp-h-[22px] rp-transition-all"><path d="M8.16004 7.99996L12.61 12.45L20.62 20.42C20.8063 20.6073 20.9108 20.8608 20.9108 21.125C20.9108 21.3891 20.8063 21.6426 20.62 21.83C20.5266 21.9226 20.4158 21.996 20.294 22.0457C20.1721 22.0955 20.0417 22.1207 19.91 22.12C19.7795 22.1241 19.6495 22.1004 19.5288 22.0504C19.4081 22.0004 19.2994 21.9252 19.21 21.83L12.63 15.28V18.2C12.63 18.4652 12.5247 18.7195 12.3372 18.9071C12.1496 19.0946 11.8953 19.2 11.63 19.2C11.4472 19.2017 11.2673 19.1533 11.11 19.06L6.11004 16H3.61004C3.07961 16 2.5709 15.7892 2.19583 15.4142C1.82076 15.0391 1.61004 14.5304 1.61004 14V9.99996C1.61004 9.46952 1.82076 8.96082 2.19583 8.58574C2.5709 8.21067 3.07961 7.99996 3.61004 7.99996H5.33004L1.89004 4.58996C1.70174 4.40298 1.59543 4.14886 1.59449 3.88349C1.59355 3.61813 1.69807 3.36326 1.88504 3.17496C2.07202 2.98665 2.32615 2.88034 2.59151 2.8794C2.85687 2.87846 3.11174 2.98298 3.30004 3.16996L8.16004 7.99996ZM12.3071 5.08287C12.4947 5.27041 12.6 5.52476 12.6 5.78998L12.61 10.33L8.69003 6.41998L11.08 4.93998C11.2363 4.84313 11.4162 4.79123 11.6 4.78998C11.8653 4.78998 12.1196 4.89533 12.3071 5.08287ZM19.67 4.70998C19.4857 4.51902 19.2331 4.40911 18.9677 4.40442C18.7023 4.39973 18.446 4.50065 18.255 4.68498C18.0641 4.8693 17.9542 5.12194 17.9495 5.3873C17.9448 5.65267 18.0457 5.90902 18.23 6.09998C19.4978 7.41954 20.3076 9.11182 20.5399 10.9269C20.7722 12.742 20.4146 14.5837 19.52 16.18C19.3905 16.4108 19.3577 16.6836 19.429 16.9385C19.5002 17.1934 19.6696 17.4097 19.9 17.54C20.0495 17.6247 20.2182 17.6695 20.39 17.67C20.5672 17.6697 20.7412 17.6223 20.894 17.5327C21.0469 17.4431 21.1732 17.3144 21.26 17.16C22.3679 15.1894 22.8115 12.9143 22.5251 10.6719C22.2387 8.42944 21.2376 6.3389 19.67 4.70998ZM16.41 13.36C16.5421 12.9187 16.6095 12.4606 16.61 12C16.6045 10.8878 16.1837 9.81785 15.43 8.99996C15.3401 8.90278 15.2701 8.78883 15.2242 8.66463C15.1783 8.54042 15.1573 8.40839 15.1624 8.27607C15.1675 8.14374 15.1986 8.01372 15.254 7.89342C15.3093 7.77313 15.3879 7.66491 15.485 7.57496C15.5822 7.485 15.6962 7.41507 15.8204 7.36915C15.9446 7.32323 16.0766 7.30222 16.2089 7.30733C16.3412 7.31244 16.4713 7.34356 16.5916 7.39891C16.7119 7.45427 16.8201 7.53278 16.91 7.62996C17.6778 8.46974 18.2113 9.49662 18.457 10.6076C18.7027 11.7186 18.6521 12.8747 18.31 13.96C18.2464 14.1623 18.1201 14.3392 17.9493 14.465C17.7785 14.5909 17.5722 14.6591 17.36 14.66C17.2577 14.6629 17.1558 14.6459 17.06 14.61C16.8087 14.5295 16.5993 14.353 16.4775 14.1189C16.3558 13.8847 16.3315 13.6119 16.41 13.36Z"></path></svg>' : '<svg viewBox="0 0 24 24" class="rp-fill-white rp-w-[22px] rp-h-[22px] rp-transition-all"><path d="M19.45 4.71001C19.2657 4.51773 19.0125 4.40655 18.7462 4.40092C18.4799 4.39529 18.2223 4.49569 18.03 4.68001C17.8377 4.86434 17.7265 5.1175 17.7209 5.3838C17.7153 5.6501 17.8157 5.90773 18 6.10001C19.5217 7.68329 20.3715 9.79404 20.3715 11.99C20.3715 14.186 19.5217 16.2967 18 17.88C17.8618 18.021 17.7684 18.1997 17.7316 18.3937C17.6948 18.5877 17.7163 18.7882 17.7933 18.97C17.8703 19.1518 17.9994 19.3068 18.1643 19.4153C18.3292 19.5239 18.5226 19.5812 18.72 19.58C18.8547 19.5795 18.9879 19.5518 19.1116 19.4985C19.2354 19.4452 19.347 19.3675 19.44 19.27C21.3198 17.3126 22.3696 14.7039 22.3696 11.99C22.3696 9.27611 21.3198 6.66741 19.44 4.71001H19.45ZM11.38 4.79002C11.1962 4.79127 11.0163 4.84317 10.86 4.94002L5.88 8.00002H3.38C2.84957 8.00002 2.34086 8.21073 1.96579 8.5858C1.59072 8.96088 1.38 9.46958 1.38 10V14C1.38 14.5304 1.59072 15.0392 1.96579 15.4142C2.34086 15.7893 2.84957 16 3.38 16H5.9L10.9 19.06C11.0573 19.1534 11.2371 19.2018 11.42 19.2C11.6852 19.2 11.9396 19.0947 12.1271 18.9071C12.3146 18.7196 12.42 18.4652 12.42 18.2V5.78002C12.4188 5.6461 12.3907 5.5138 12.3373 5.39096C12.284 5.26812 12.2065 5.15725 12.1095 5.06491C12.0125 4.97258 11.898 4.90067 11.7727 4.85345C11.6473 4.80622 11.5138 4.78465 11.38 4.79002ZM15.9895 7.29371C16.2538 7.30683 16.5021 7.42417 16.68 7.62001L16.71 7.60001C17.8006 8.7941 18.4053 10.3528 18.4053 11.97C18.4053 13.5872 17.8006 15.1459 16.71 16.34C16.6165 16.4436 16.5024 16.5265 16.3749 16.5833C16.2475 16.6401 16.1096 16.6697 15.97 16.67C15.7222 16.6694 15.4834 16.5767 15.3 16.41C15.1042 16.2321 14.9868 15.9838 14.9737 15.7195C14.9606 15.4552 15.0528 15.1965 15.23 15C15.9722 14.1766 16.3852 13.1086 16.39 12C16.3845 10.8879 15.9636 9.81791 15.21 9.00001C15.1154 8.90258 15.0417 8.78691 14.9933 8.66005C14.9448 8.53318 14.9228 8.39779 14.9284 8.26211C14.934 8.12644 14.9672 7.99333 15.0259 7.8709C15.0846 7.74848 15.1677 7.63929 15.27 7.55001C15.4665 7.37275 15.7252 7.28059 15.9895 7.29371Z"></path></svg>';
            });

            fullscreenBtn.addEventListener('click', function() {
                if (videoElement.requestFullscreen) {
                    videoElement.requestFullscreen();
                } else if (videoElement.mozRequestFullScreen) {
                    videoElement.mozRequestFullScreen();
                } else if (videoElement.webkitRequestFullscreen) {
                    videoElement.webkitRequestFullscreen();
                } else if (videoElement.msRequestFullscreen) {
                    videoElement.msRequestFullscreen();
                }
            });

            // حذف رویداد کلیک روی seekBar طبق درخواست
            // seekBar.addEventListener('click', onSeek); 

            videoElement.addEventListener('timeupdate', updateSeekBar);
            
            videoElement.addEventListener('loadedmetadata', function() {
                updateSeekBar();
            });

            function updateEpisodeButtons() {
                prevEpisodeBtn.style.display = currentEpisodeIndex > 0 ? 'flex' : 'none';
                nextEpisodeBtn.style.display = currentEpisodeIndex < episodes.length - 1 ? 'flex' : 'none';
            }

            prevEpisodeBtn.addEventListener('click', function() {
                currentEpisodeIndex--;
                loadEpisode();
            });

            nextEpisodeBtn.addEventListener('click', function() {
                currentEpisodeIndex++;
                loadEpisode();
            });

            function loadEpisode() {
                loadingSpinner.style.display = 'block';
                // ریست کردن پلیر
                progress.style.width = '0%';
                videoElement.currentTime = 0;
                
                sources = episodes[currentEpisodeIndex].sources;
                nextEpisodeTitle.textContent = episodes[currentEpisodeIndex].title;
                
                // شروع از کیفیت اول (پیش‌فرض کمترین حجم)
                loadVideoWithFallback(0);
                
                updateEpisodeButtons();
            }

            updateEpisodeButtons();
            
            // اولین بار فیلم رو پخش می‌کنیم
            videoElement.style.display = 'block';
            document.body.appendChild(videoElement);
            playButton.style.display = 'none';
            
            $('playIcon').click(function() {
                loadEpisode();
                $(this).hide();
                updatePlayPauseIcon(true);
            });

            let loadingSpinner = document.getElementById('loading-spinner');

            // نمایش لودینگ هنگام تلاش برای پخش
            videoElement.addEventListener('waiting', function() {
                loadingSpinner.style.display = 'block';
            });

            // مخفی کردن لودینگ وقتی ویدیو پخش شد
            videoElement.addEventListener('playing', function() {
                loadingSpinner.style.display = 'none';
            });

            // مخفی کردن لودینگ وقتی ویدیو به صورت خودکار پخش شد (برای مرورگرهایی که waiting را درست ارسال نمی‌کنند)
            videoElement.addEventListener('canplay', function() {
                loadingSpinner.style.display = 'none';
            });
        });
    </script>
</body>
</html>
```