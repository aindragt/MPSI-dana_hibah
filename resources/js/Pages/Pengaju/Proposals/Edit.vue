<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3'
import PengajuLayout from '@/Layouts/PengajuLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'

const props = defineProps({
    proposal: {
        type: Object,
        required: true,
    },
})

const form = useForm({
    activity_title: props.proposal.activity_title || '',
    activity_description: props.proposal.activity_description || '',
    total_budget: props.proposal.total_budget || '',
    execution_start_date: props.proposal.execution_start_date || '',
    execution_end_date: props.proposal.execution_end_date || '',
})

const submit = () => {
    form.put(route('pengaju.proposals.update', props.proposal.id))
}
</script>

<template>
    <Head :title="`Edit Proposal - ${proposal.proposal_number}`" />

    <PengajuLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Edit Proposal ({{ proposal.proposal_number }})
                </h2>
                <Link
                    :href="route('pengaju.proposals.show', proposal.id)"
                    class="text-sm text-gray-600 hover:text-gray-900"
                >
                    &larr; Batal & Kembali
                </Link>
            </div>
        </template>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
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
                            :href="route('pengaju.proposals.show', proposal.id)"
                            class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50"
                        >
                            Batal
                        </Link>
                        <PrimaryButton :disabled="form.processing">
                            Simpan Perubahan
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </PengajuLayout>
</template>
