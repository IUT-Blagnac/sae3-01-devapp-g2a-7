<div id="info-popup" onclick="close_info_popup()"></div>

<style>
    #info-popup {
        text-align:justify;
        padding:20px;
        position:fixed;
        top:50%;
        left:50%;
        max-width:700px;
        transform:translate(-50%, -50%);
        opacity:0.0;
        z-index:10;
        box-shadow:1px 1px 5px var(--dark-grey);
        background-color:white;
        border-radius:10px;
        transition-duration:1s;
    }
    #info-popup:hover {
        cursor:pointer;
    }
</style>

<script type="text/javascript">
    function close_info_popup() {
        document.getElementById("info-popup").style.opacity = "0.0";
        setTimeout(() => {
            document.getElementById("info-popup").style.display = "none";
        }, 1100);
    }
    function show_info_popup(message, color) {
        document.getElementById("info-popup").textContent = message;
        document.getElementById("info-popup").style.color = color;
        document.getElementById("info-popup").style.display = "block";
        setTimeout(() => {
            document.getElementById("info-popup").style.opacity = "1.0";
        }, 100);
        setTimeout(() => {
            close_info_popup();
        }, 6000);
    }
</script>
