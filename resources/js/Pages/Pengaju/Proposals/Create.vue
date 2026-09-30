<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3'
import PengajuLayout from '@/Layouts/PengajuLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'

const props = defineProps({
    activeWindow: {
        type: Object,
        default: null,
    },
})

const form = useForm({
    activity_title: '',
    activity_description: '',
    total_budget: '',
    execution_start_date: '',
    execution_end_date: '',
})

const submit = () => {
    form.post(route('pengaju.proposals.store'))
}
</script>

<template>
    <Head title="Buat Proposal Baru" />

    <PengajuLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Buat Proposal Baru
                </h2>
                <Link
                    :href="route('pengaju.proposals.index')"
                    class="text-sm text-gray-600 hover:text-gray-900"
                >
                    &larr; Kembali ke Daftar
                </Link>
            </div>
        </template>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div v-if="!activeWindow" class="mb-6 rounded-md bg-yellow-50 p-4">
                    <div class="flex">
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">
                                Jendela Pengajuan Tidak Aktif
                            </h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                Saat ini tidak ada periode jendela pengajuan aktif. Anda tidak dapat membuat proposal baru.
                            </div>
                        </div>
                    </div>
                </div>

                <form @submit.prevent="submit" class="max-w-2xl space-y-6">
                    <div>
                        <InputLabel for="activity_title" value="Judul Kegiatan" />
                        <TextInput
                            id="activity_title"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.activity_title"
                            required
                            autofocus
                        />
                        <InputError class="mt-2" :message="form.errors.activity_title" />
                    </div>

                    <div>
                        <InputLabel for="activity_description" value="Deskripsi Kegiatan" />
                        <textarea
                            id="activity_description"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            rows="4"
                            v-model="form.activity_description"
                        ></textarea>
                        <InputError class="mt-2" :message="form.errors.activity_description" />
                    </div>

                    <div>
                        <InputLabel for="total_budget" value="Total Anggaran (Rp)" />
                        <TextInput
                            id="total_budget"
                            type="number"
                            min="0"
                            step="1000"
                            class="mt-1 block w-full"
                            v-model="form.total_budget"
                            required
                        />
                        <InputError class="mt-2" :message="form.errors.total_budget" />
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <InputLabel for="execution_start_date" value="Tanggal Mulai Pelaksanaan" />
                            <TextInput
                                id="execution_start_date"
                                type="date"
                                class="mt-1 block w-full"
                                v-model="form.execution_start_date"
                            />
                            <InputError class="mt-2" :message="form.errors.execution_start_date" />
                        </div>

                        <div>
                            <InputLabel for="execution_end_date" value="Tanggal Selesai Pelaksanaan" />
                            <TextInput
                                id="execution_end_date"
                                type="date"
                                class="mt-1 block w-full"
                                v-model="form.execution_end_date"
                            />
                            <InputError class="mt-2" :message="form.errors.execution_end_date" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4">
                        <Link
                            :href="route('pengaju.proposals.index')"
                            class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25"
                        >
                            Batal
                        </Link>
                        <PrimaryButton :disabled="form.processing || !activeWindow">
                            Simpan Proposal
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </PengajuLayout>
</template>
