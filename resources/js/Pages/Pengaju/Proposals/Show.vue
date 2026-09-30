<script setup>
import { computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import PengajuLayout from '@/Layouts/PengajuLayout.vue'
import StatusBadge from '@/Components/proposal/StatusBadge.vue'
import DocumentChecklist from '@/Components/proposal/DocumentChecklist.vue'

const props = defineProps({
    proposal: {
        type: Object,
        required: true,
    },
    documentTypes: {
        type: Array,
        default: () => [],
    },
})

const formatCurrency = (val) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val)
}

const formatDate = (dateString) => {
    if (!dateString) return '-'
    const date = new Date(dateString)
    return new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }).format(date)
}

// Calculate unique uploaded document types count
const uploadedUniqueDocTypesCount = computed(() => {
    if (!props.proposal.documents) return 0
    const types = new Set(props.proposal.documents.map((d) => d.document_type_id))
    return types.size
})

const isAllDocumentsUploaded = computed(() => {
    return props.documentTypes.length > 0 && uploadedUniqueDocTypesCount.value >= props.documentTypes.length
})

const isEditable = computed(() => {
    return props.proposal.status === 'draft' || props.proposal.status === 'perlu_revisi'
})
</script>

<template>
    <Head :title="`Detail Proposal - ${proposal.proposal_number}`" />

    <PengajuLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Detail Proposal
                </h2>
                <div class="flex items-center space-x-3">
                    <Link
                        v-if="isEditable"
                        :href="route('pengaju.proposals.edit', proposal.id)"
                        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Edit Proposal
                    </Link>
                    <Link
                        :href="route('pengaju.proposals.index')"
                        class="text-sm text-gray-600 hover:text-gray-900"
                    >
                        &larr; Kembali
                    </Link>
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Details Card -->
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <div class="flex items-center justify-between border-b pb-4 mb-4">
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Nomor Proposal</span>
                        <h3 class="text-lg font-bold text-gray-900">{{ proposal.proposal_number }}</h3>
                    </div>
                    <StatusBadge :status="proposal.status" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Judul Kegiatan</p>
                        <p class="text-sm font-semibold text-gray-900 mt-1">{{ proposal.activity_title }}</p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500 font-medium">Total Anggaran</p>
                        <p class="text-sm font-semibold text-indigo-600 mt-1">{{ formatCurrency(proposal.total_budget) }}</p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500 font-medium">Periode Pelaksanaan</p>
                        <p class="text-sm text-gray-900 mt-1">
                            {{ formatDate(proposal.execution_start_date) }} s/d {{ formatDate(proposal.execution_end_date) }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500 font-medium">Tanggal Dibuat</p>
                        <p class="text-sm text-gray-900 mt-1">{{ formatDate(proposal.created_at) }}</p>
                    </div>

                    <div class="md:col-span-2" v-if="proposal.activity_description">
                        <p class="text-xs text-gray-500 font-medium">Deskripsi Kegiatan</p>
                        <p class="text-sm text-gray-700 mt-1 whitespace-pre-line">{{ proposal.activity_description }}</p>
                    </div>
                </div>
            </div>

            <!-- Document Checklist Component -->
            <DocumentChecklist
                :proposal-id="proposal.id"
                :document-types="documentTypes"
                :uploaded-documents="proposal.documents"
                :is-editable="isEditable"
            />

            <!-- Actions & Submit Section -->
            <div class="bg-white p-6 shadow-sm sm:rounded-lg flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-900">
                        Status Dokumen: {{ uploadedUniqueDocTypesCount }} / {{ documentTypes.length }} Terunggah
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ isAllDocumentsUploaded ? 'Semua dokumen wajib telah lengkap. Anda dapat mengajukan proposal ini.' : 'Harap lengkapi semua 11 dokumen wajib untuk dapat mengajukan proposal.' }}
                    </p>
                </div>

                <div class="flex items-center space-x-4">
                    <button
                        v-if="isEditable && isAllDocumentsUploaded"
                        type="button"
                        class="inline-flex items-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-green-700 focus:outline-none"
                    >
                        Ajukan Proposal
                    </button>
                </div>
            </div>
        </div>
    </PengajuLayout>
</template>
