<div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered"> <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-white border-bottom-0 pt-4 px-4 justify-content-center">
                <div class="text-center">
                    <h5 class="modal-title fw-bold text-dark">LOGIN ADMINISTRATOR</h5>
                    <p class="text-muted small mb-0">Masukkan kredensial PIC</p>
                </div>
            </div>
            <form action="proses_login.php" method="POST">
                <div class="modal-body py-3 px-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary text-uppercase">Username</label>
                        <input type="text" name="username" class="form-control bg-light border-0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary text-uppercase">Password</label>
                        <input type="password" name="password" class="form-control bg-light border-0" required>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="submit" class="btn btn-dark w-100 fw-bold py-2">MASUK SISTEM</button>
                </div>
            </form>
        </div>
    </div>
</div>