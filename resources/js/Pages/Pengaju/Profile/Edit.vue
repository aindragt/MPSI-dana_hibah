<script setup>
import PengajuLayout from '@/Layouts/PengajuLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    user: Object,
    organizationProfile: Object,
});

const form = useForm({
    organization_name: props.organizationProfile?.organization_name || props.user?.name || '',
    address: props.organizationProfile?.address || props.user?.alamat || '',
    district: props.organizationProfile?.district || '',
    village: props.organizationProfile?.village || '',
    field_of_activity: props.organizationProfile?.field_of_activity || '',
    chairman_name: props.organizationProfile?.chairman_name || props.user?.nama_ketua || '',
    secretary_name: props.organizationProfile?.secretary_name || '',
    treasurer_name: props.organizationProfile?.treasurer_name || '',
    organization_phone: props.organizationProfile?.organization_phone || props.user?.no_wa || '',
    organization_email: props.organizationProfile?.organization_email || props.user?.email || '',

    foto_profil: null,
    file_akta: null,
    file_kesbangpol: null,
    rekening_lembaga: null,
    npwp_lembaga: null,
});

const submit = () => {
    form.post(route('pengaju.profile.update'), {
        preserveScroll: true,
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Profil Organisasi" />

    <PengajuLayout>
        <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8 space-y-6">
            <div class="p-6 bg-white shadow sm:rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Profil Organisasi</h3>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Data Organisasi -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Lembaga / Organisasi *</label>
                            <input v-model="form.organization_name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required />
                            <div v-if="form.errors.organization_name" class="text-red-500 text-xs mt-1">{{ form.errors.organization_name }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Bidang Kegiatan</label>
                            <input v-model="form.field_of_activity" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            <div v-if="form.errors.field_of_activity" class="text-red-500 text-xs mt-1">{{ form.errors.field_of_activity }}</div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Alamat Lengkap *</label>
                            <textarea v-model="form.address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="3" required></textarea>
                            <div v-if="form.errors.address" class="text-red-500 text-xs mt-1">{{ form.errors.address }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kecamatan</label>
                            <input v-model="form.district" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Desa / Kelurahan</label>
                            <input v-model="form.village" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Telepon Organisasi</label>
                            <input v-model="form.organization_phone" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email Organisasi</label>
                            <input v-model="form.organization_email" type="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                    </div>

                    <!-- Pengurus Organisasi -->
                    <hr />
                    <h4 class="text-md font-medium text-gray-800">Susunan Pengurus</h4>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Ketua</label>
                            <input v-model="form.chairman_name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Sekretaris</label>
                            <input v-model="form.secretary_name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Bendahara</label>
                            <input v-model="form.treasurer_name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                    </div>

                    <!-- Berkas Legalitas -->
                    <hr />
                    <h4 class="text-md font-medium text-gray-800">Berkas Legalitas & Identitas</h4>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Foto Profil / Lambang (JPG/PNG, max 5MB)</label>
                            <input type="file" @change="e => form.foto_profil = e.target.files[0]" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                            <p v-if="props.user.foto_profil" class="text-xs text-gray-500 mt-1">File saat ini: {{ props.user.foto_profil }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">File Akta Pendirian (PDF/Gambar, max 5MB)</label>
                            <input type="file" @change="e => form.file_akta = e.target.files[0]" accept=".pdf,image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                            <p v-if="props.user.file_akta" class="text-xs text-gray-500 mt-1">File saat ini: {{ props.user.file_akta }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">File SK Kesbangpol / Kemenkumham (PDF/Gambar, max 5MB)</label>
                            <input type="file" @change="e => form.file_kesbangpol = e.target.files[0]" accept=".pdf,image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                            <p v-if="props.user.file_kesbangpol" class="text-xs text-gray-500 mt-1">File saat ini: {{ props.user.file_kesbangpol }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">File Rekening Bank Lembaga (PDF/Gambar, max 5MB)</label>
                            <input type="file" @change="e => form.rekening_lembaga = e.target.files[0]" accept=".pdf,image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                            <p v-if="props.user.rekening_lembaga" class="text-xs text-gray-500 mt-1">File saat ini: {{ props.user.rekening_lembaga }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">File NPWP Lembaga (PDF/Gambar, max 5MB)</label>
                            <input type="file" @change="e => form.npwp_lembaga = e.target.files[0]" accept=".pdf,image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                            <p v-if="props.user.npwp_lembaga" class="text-xs text-gray-500 mt-1">File saat ini: {{ props.user.npwp_lembaga }}</p>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </PengajuLayout>
</template>
