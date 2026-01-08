MyStudy – Smart Study Time Recommender

1. Gambaran Proyek

MyStudy adalah aplikasi berbasis web yang menggunakan AI untuk membantu mahasiswa menentukan waktu belajar paling efektif berdasarkan data tidur dan jadwal kuliah. Sistem ini memprediksi tingkat produktivitas setiap jam dan memberikan rekomendasi waktu belajar yang optimal secara personal dan adaptif.
Proyek ini mendukung SDG 4 – Quality Education

2. Anggota Kelompok

- Kevin Artan – 0706012310032
- Michelle Valensia – 0706012310012
- Nathaniel Michael – 0706012310042

3. Permasalahan

Mahasiswa sering kesulitan menentukan waktu belajar yang efektif karena:
- Pola tidur tidak teratur
- Jadwal kuliah padat
- Kelelahan dan stres

Sebagian besar aplikasi hanya menyediakan jadwal statis dan tidak menyesuaikan dengan kebiasaan individu.
MyStudy AI hadir untuk memberikan rekomendasi waktu belajar yang berbasis data dan dapat beradaptasi.

4. Pendekatan AI
   
Jenis Prediksi: Regresi
Output: skor produktivitas 0.0 – 1.0 untuk setiap jam
Model yang Digunakan: Random Forest Regressor

Model dilatih satu kali dan disimpan sebagai file .pkl
Digunakan oleh semua pengguna sebagai model dasar

Model melatih prediksi produktivitas dari:
- Waktu
- Durasi tidur
- Hari kerja / akhir pekan
- Tingkat stres

5. AI Adaptif & Personalisasi

Untuk menyesuaikan dengan perbedaan kebiasaan tiap pengguna, sistem menggunakan lapisan adaptasi personal.

Cara Kerja:
- Semua pengguna menggunakan model AI yang sama
- AI memberikan rekomendasi waktu belajar
- Pengguna memberi umpan balik (tingkat efektivitas)
- Sistem menyesuaikan rekomendasi khusus per pengguna
- Model AI tidak dilatih ulang

Dengan mekanisme ini, rekomendasi menjadi semakin sesuai dengan preferensi masing-masing pengguna.

6. Dataset yang Digunakan

Sleep Health and Lifestyle Dataset
https://www.kaggle.com/datasets/uom190346a/sleep-health-and-lifestyle-dataset

Dataset ini berisi data terkait:
- Durasi tidur
- Kualitas tidur
- Kebiasaan dan gaya hidup

Dataset disesuaikan untuk kebutuhan prediksi waktu belajar.

7. Alur Sistem Singkat

- Pengguna mengisi data tidur dan jadwal kuliah
- AI memprediksi produktivitas per jam
- Jam sibuk dieliminasi dengan aturan tambahan
- AI merekomendasikan waktu belajar terbaik
- Umpan balik pengguna meningkatkan akurasi rekomendasi

8. Ringkasan

- Model AI dilatih sekali
- Rekomendasi semakin personal seiring waktu
- Tidak ada pelatihan ulang per pengguna
- Sistem sederhana, adaptif, dan mudah dikembangkan
