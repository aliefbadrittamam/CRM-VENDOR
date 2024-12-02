module.exports = {
    darkMode: 'class', // atau 'media' jika ingin otomatis berdasarkan browser
    content: [
        './resources/**/*.blade.php', // Sesuaikan dengan struktur proyek Laravel Anda
        './resources/**/*.js',
        './node_modules/flowbite/**/*.js' // Tambahkan ini untuk Flowbite
    ],
    theme: {
        extend: {},
    },
    plugins: [
        require('flowbite/plugin') // Daftarkan plugin Flowbite
    ],
};
