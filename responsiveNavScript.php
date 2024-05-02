<script src="https://unpkg.com/feather-icons"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script>
        feather.replace();
        const mobileNav = document.querySelector("#mobileNav");
        const menu = document.querySelector("#menu");
        menu.addEventListener("click", e => {
            if (mobileNav.classList.contains("hidden")) {
                mobileNav.classList.remove("hidden");
                mobileNav.classList.add("block");
            } else {
                mobileNav.classList.add("hidden");
                mobileNav.classList.remove("block");
            }
        })
    </script>