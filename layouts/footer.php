</main> <footer class="footer-fixed">
        <p>&copy; <?= date('Y'); ?> Sistem Perpustakaan The Iwaks.</p>
    </footer>

    <div class="modal-overlay" id="customModal">
        <div class="modal-box">
            <div class="modal-title">Konfirmasi</div>
            <div class="modal-text" id="modalText">Apakah Anda yakin ingin melakukan tindakan ini?</div>
            <div class="modal-actions">
                <button class="btn-modal-cancel" onclick="closeModal()">Batal</button>
                <a href="#" class="btn-modal-confirm" id="modalConfirmBtn">Ya, Lanjutkan</a>
            </div>
        </div>
    </div>
    <script>
        function showModal(pesan, action) {
            document.getElementById('modalText').innerText = pesan; 
            const confirmBtn = document.getElementById('modalConfirmBtn');
            
            if (typeof action === 'function') {
                confirmBtn.href = '#';
                confirmBtn.onclick = async function(e) {
                    e.preventDefault();
                    await action();
                    closeModal();
                };
            } else {
                confirmBtn.href = action;
                confirmBtn.onclick = null;
            }
            
            document.getElementById('customModal').classList.add('active'); 
        }

        function closeModal() {
            document.getElementById('customModal').classList.remove('active'); 
        }
    </script>
</body>
</html>