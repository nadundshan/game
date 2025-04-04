<!-- music_player.php -->
<div class="music-controls">
    <button id="music-toggle" onclick="toggleMusic()">🔊</button>
    <input type="range" id="volume-slider" min="0" max="1" step="0.1" value="0.5">
</div>

<audio id="bg-music" loop>
    <source src="audio_tracks\background_music.mp3" type="audio/mp3">
</audio>

<script>
    let music = document.getElementById("bg-music");
    let musicToggle = document.getElementById("music-toggle");
    let volumeSlider = document.getElementById("volume-slider");

    // Auto-play music when page loads
    document.addEventListener("DOMContentLoaded", function() {
        music.volume = 0.1;
        music.play().catch(error => {
            console.log("Autoplay blocked by browser, user must interact first.");
        });
    });

    function toggleMusic() {
        if (music.paused) {
            music.play();
            musicToggle.textContent = "🔊";
        } else {
            music.pause();
            musicToggle.textContent = "🔇";
        }
    }

    // Adjust volume with slider
    volumeSlider.addEventListener("input", function() {
        music.volume = volumeSlider.value;
    });
</script>

<style>
    /* Music Controls Styling */
    .music-controls {
        position: absolute;
        bottom: 20px;
        right: 20px;
        background: rgba(255, 255, 255, 0.8);
        padding: 10px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .music-controls button {
        background: rgb(27, 97, 201);
        border: none;
        padding: 8px 12px;
        border-radius: 5px;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .music-controls button:hover {
        background: #e6b800;
    }

    .music-controls input {
        width: 100px;
    }
</style>
