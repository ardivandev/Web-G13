<script>
  // GANTI SELURUH BAGIAN SCRIPT DI BLADE FILE DENGAN INI:
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing...'); // Debug

    const audio = document.getElementById('notifAudio');
    const tableBody = document.querySelector('#table-body');
    const counterBadge = document.querySelector('.counter-peminjaman');

    // Pastikan toast container ada
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        console.log('Creating toast container');
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container';
        toastContainer.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; pointer-events: none;';
        document.body.appendChild(toastContainer);
    }

    // Function untuk menghapus toast
    window.removeToast = function(toastId) {
        console.log('Removing toast:', toastId);
        const toastElement = document.getElementById(toastId);
        if (toastElement) {
            toastElement.style.transition = 'all 0.3s ease-out';
            toastElement.style.transform = 'translateX(100%)';
            toastElement.style.opacity = '0';

            setTimeout(() => {
                if (toastElement && toastElement.parentNode) {
                    toastElement.remove();
                    console.log('Toast removed successfully');
                }
            }, 300);
        }
    };

    // Function untuk menampilkan toast
    function showToast(title, message, type = 'info') {
        console.log('Showing toast:', { title, message, type });

        const toastId = 'toast-' + Date.now();
        let bgClass, iconClass;

        switch(type) {
            case 'success':
                bgClass = 'alert-success';
                iconClass = 'fas fa-check-circle';
                break;
            case 'danger':
                bgClass = 'alert-danger';
                iconClass = 'fas fa-exclamation-triangle';
                break;
            default:
                bgClass = 'alert-info';
                iconClass = 'fas fa-info-circle';
        }

        const toastHTML = `
            <div id="${toastId}" class="alert ${bgClass} alert-dismissible fade show shadow-lg"
                 role="alert"
                 style="pointer-events: auto; margin-bottom: 10px; min-width: 300px; animation: slideInRight 0.3s ease-out;">
                <i class="${iconClass} me-2"></i>
                <strong>${title}</strong><br>
                <small>${message}</small>
                <button type="button" class="btn-close" onclick="removeToast('${toastId}')" aria-label="Close"></button>
            </div>
        `;

        toastContainer.insertAdjacentHTML('beforeend', toastHTML);

        // Auto remove setelah 5 detik
        setTimeout(() => {
            removeToast(toastId);
        }, 5000);
    }

    // Function to update connection status
    function updateConnectionStatus(status, message) {
        const connectionStatus = document.getElementById('connection-status');
        const statusText = document.getElementById('status-text');

        if (connectionStatus && statusText) {
            connectionStatus.className = `small text-muted connection-status ${status}`;
            statusText.textContent = message;

            const icon = connectionStatus.querySelector('i');
            if (icon) {
                if (status === 'connected') {
                    icon.className = 'fas fa-circle text-success';
                } else if (status === 'connecting') {
                    icon.className = 'fas fa-circle text-warning';
                } else {
                    icon.className = 'fas fa-circle text-danger';
                }
            }
        }
    }

    // Function to play notification sound
    function playNotificationSound() {
        if (audio) {
            audio.play().catch(e => {
                console.log('Audio autoplay blocked:', e);
            });
        }
    }

    // Function to render row HTML
    function renderRow(p, isNew = false) {
        const status = (p.status || 'menunggu').toLowerCase();
        const roleBadge = p.role?.toLowerCase() === 'siswa' ?
            '<span class="badge bg-info text-white">Siswa</span>' :
            p.role?.toLowerCase() === 'guru' ?
            '<span class="badge bg-success text-white">Guru</span>' :
            '<span class="badge bg-secondary text-white">N/A</span>';

        let namaUser = '-';
        if(p.role?.toLowerCase() === 'siswa' && p.siswa){
            namaUser = p.siswa.nama_siswa || 'Tidak ada nama';
            if(p.siswa.kelas) namaUser += ` <small class="text-muted d-block">(${p.siswa.kelas})</small>`;
        } else if(p.role?.toLowerCase() === 'guru' && p.guru){
            namaUser = (p.guru.nama_guru || 'Tidak ada nama') + ' <small class="text-muted d-block">Guru</small>';
        }

        const mapel = p.mapel?.nama_mapel || '-';
        const ruangan = p.ruangan?.nama_ruangan || '-';

        const barangHTML = p.detail?.length > 0 ?
            p.detail.map(d => `<span class="barang-item">${d.barang?.nama_barang || '-'}(<span class="barang-quantity">${d.jumlah || 0}</span>)</span>`).join(' ') :
            '<span class="text-muted">-</span>';

        const mulaiKBM = p.mulai_kbm ? new Date(p.mulai_kbm + ' 00:00:00').toLocaleDateString('id-ID') + ' ' + p.mulai_kbm.slice(-5) : '-';
        const selesaiKBM = p.selesai_kbm ? new Date(p.selesai_kbm + ' 00:00:00').toLocaleDateString('id-ID') + ' ' + p.selesai_kbm.slice(-5) : '-';

        let statusBadgeClass = 'bg-secondary', statusIcon='fa-question';
        if(status==='menunggu'){statusBadgeClass='bg-warning';statusIcon='fa-clock';}
        else if(status==='dipinjam'){statusBadgeClass='bg-success';statusIcon='fa-check';}
        else if(status==='ditolak'){statusBadgeClass='bg-danger';statusIcon='fa-times';}
        else if(status==='selesai'){statusBadgeClass='bg-info';statusIcon='fa-check-circle';}

        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        const editUrl = `/petugas/peminjaman/${p.id_pinjam}/edit`;
        const deleteUrl = `/petugas/peminjaman/${p.id_pinjam}`;
        const updateStatusUrl = `/petugas/peminjaman/${p.id_pinjam}/status`;

        const actionButtons = `
            <a href="${editUrl}" class="btn btn-sm btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <form action="${deleteUrl}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin menghapus?')">
                <input type="hidden" name="_token" value="${csrf}">
                <input type="hidden" name="_method" value="DELETE">
                <button type="submit" class="btn btn-sm btn-danger">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </form>
            ${status === 'menunggu' ? `
                <form action="${updateStatusUrl}" method="POST" style="display:inline-block;">
                    <input type="hidden" name="_token" value="${csrf}">
                    <input type="hidden" name="status" value="Dipinjam">
                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Setujui peminjaman ini?')">
                        <i class="fas fa-check"></i> Setujui
                    </button>
                </form>
                <form action="${updateStatusUrl}" method="POST" style="display:inline-block;">
                    <input type="hidden" name="_token" value="${csrf}">
                    <input type="hidden" name="status" value="Ditolak">
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tolak peminjaman ini?')">
                        <i class="fas fa-times"></i> Tolak
                    </button>
                </form>
            ` : ''}
        `;

        const rowClass = isNew ? 'table-success' : '';
        const counter = isNew ? 'BARU' : document.querySelectorAll('#table-body tr').length + 1;

        return `
            <tr id="peminjaman-${p.id_pinjam}" class="${rowClass}" data-new-row="${isNew}">
                <td>${counter}</td>
                <td>${roleBadge}</td>
                <td>${namaUser}</td>
                <td>${mapel}</td>
                <td>${ruangan}</td>
                <td class="barang-list">${barangHTML}</td>
                <td>${mulaiKBM}</td>
                <td>${selesaiKBM}</td>
                <td>
                    <span class="badge status-badge ${statusBadgeClass}">
                        <i class="fas ${statusIcon}"></i> ${status.charAt(0).toUpperCase() + status.slice(1)}
                    </span>
                </td>
                <td class="action-buttons">${actionButtons}</td>
            </tr>
        `;
    }

    // Function to update existing row
    function updateExistingRow(p) {
        const existingRow = document.getElementById(`peminjaman-${p.id_pinjam}`);
        if (existingRow) {
            existingRow.className = 'table-warning';
            existingRow.outerHTML = renderRow(p, false);

            setTimeout(() => {
                const updatedRow = document.getElementById(`peminjaman-${p.id_pinjam}`);
                if (updatedRow) {
                    updatedRow.classList.remove('table-warning');
                }
            }, 3000);
        }
    }

    // Function to remove empty row if exists
    function removeEmptyRow() {
        const emptyRow = document.getElementById('empty-row');
        if (emptyRow) {
            emptyRow.remove();
        }
    }

    // Test toast (hapus setelah berhasil)
    setTimeout(() => {
        showToast('🧪 Test Toast', 'Jika kamu melihat ini, toast sudah berfungsi!', 'success');
    }, 2000);

    // Initialize Pusher + Echo
    updateConnectionStatus('connecting', 'Menghubungkan...');

    window.Pusher = Pusher;
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: '{{ env("PUSHER_APP_KEY") }}',
        cluster: '{{ env("PUSHER_APP_CLUSTER") }}',
        forceTLS: true
    });

    // Connection event listeners
    window.Echo.connector.pusher.connection.bind('connected', function() {
        updateConnectionStatus('connected', 'Terhubung');
        console.log('Pusher connected successfully');
    });

    window.Echo.connector.pusher.connection.bind('disconnected', function() {
        updateConnectionStatus('error', 'Terputus');
        console.log('Pusher disconnected');
    });

    window.Echo.connector.pusher.connection.bind('error', function(error) {
        updateConnectionStatus('error', 'Error');
        console.error('Pusher connection error:', error);
    });

    // Listen for new peminjaman
    window.Echo.channel('gudang13').listen('.peminjaman.baru', (e) => {
        console.log("Peminjaman baru diterima:", e);

        if (e.peminjaman) {
            const p = e.peminjaman;

            playNotificationSound();

            const userName = p.role?.toLowerCase() === 'siswa' ?
                (p.siswa?.nama_siswa || 'Siswa') :
                (p.guru?.nama_guru || 'Guru');

            showToast(
                '🔔 Peminjaman Baru!',
                `${userName} mengajukan peminjaman ${p.detail?.length || 0} jenis barang`,
                'info'
            );

            removeEmptyRow();
            tableBody.insertAdjacentHTML('afterbegin', renderRow(p, true));

            if (counterBadge) {
                const currentCount = parseInt(counterBadge.textContent);
                counterBadge.textContent = currentCount + 1;
            }

            setTimeout(() => {
                const newRow = document.getElementById(`peminjaman-${p.id_pinjam}`);
                if (newRow) {
                    newRow.classList.remove('table-success');
                    newRow.setAttribute('data-new-row', 'false');
                }
            }, 5000);
        }
    });

    // Listen for status updates
    window.Echo.channel('gudang13').listen('.peminjaman.status.update', (e) => {
        console.log("Status peminjaman diperbarui:", e);

        if (e.peminjaman) {
            const p = e.peminjaman;

            showToast(
                '📝 Status Diperbarui',
                `Peminjaman #${p.id_pinjam} status: ${p.status}`,
                'success'
            );

            updateExistingRow(p);
        }
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            if (alert.classList.contains('show')) {
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 150);
            }
        });
    }, 3000);
});
</script>
