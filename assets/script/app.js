let navbar = `
    <nav class="navbar navbar-expand-lg bg-white shadow-sm py-3 mb-5">
                    <div class="container">
                        <a class="navbar-brand fw-bold text-dark" href="#">
                    Egy-Hills
                        </a>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarNav">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                                <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
                                <li class="nav-item"><a class="nav-link" href="#">About Us</a></li>
                                <li class="nav-item"><a class="nav-link" href="#">Projects</a></li>
                                <li class="nav-item"><a class="nav-link" href="#">Services</a></li>
                                <li class="nav-item"><a class="nav-link" href="#">Privacy Policy</a></li>

                                          <li class="nav-item"><a class="nav-link" href="#">Booking</a></li>

                                <li class="nav-item"><a class="nav-link" href="#">Partners</a></li>

                            </ul>
                        </div>
                        <div class="d-none d-lg-flex align-items-center">
                            <span class="me-3 text-muted">Call Us: <span class="text-dark">+(084) 123 - 456
                                    88</span></span>
                            <a href="#" class="btn btn-warning fw-bold">
Contact Us</a>
                        </div>
                    </div>
                </nav>

`;

let nav_bar = document.getElementById("nav_bar");
nav_bar.innerHTML = navbar;
