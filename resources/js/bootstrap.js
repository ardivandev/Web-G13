import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: "pusher",
    key: import.meta.env.VITE_PUSHER_APP_KEY, // 🔑 ambil dari .env
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
});

window.Echo.channel("gudang13")
    .listen(".peminjaman.baru", (e) => {
        console.log("📦 Peminjaman Baru:", e);
    })
    .listen(".peminjaman.status.update", (e) => {
        console.log("🔄 Status Update:", e);
    })
    .listen(".status.gudang.updated", (e) => {
        console.log("📢 Status Gudang:", e.status);
    });
