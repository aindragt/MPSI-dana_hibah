<script setup>
import { computed } from 'vue'
import FileUpload from '@/Components/ui/FileUpload.vue'

const props = defineProps({
    proposalId: {
        type: [Number, String],
        required: true,
    },
    documentTypes: {
        type: Array,
        required: true,
    },
    uploadedDocuments: {
        type: Array,
        default: () => [],
    },
    isEditable: {
        type: Boolean,
        default: false,
    },
})

// Map latest uploaded document per document_type_id
const uploadedDocMap = computed(() => {
    const map = {}
    props.uploadedDocuments.forEach((doc) => {
        if (!map[doc.document_type_id] || doc.version > map[doc.document_type_id].version) {
            map[doc.document_type_id] = doc
        }
    })
    return map
})
</script>

<template>
    <div class="bg-white p-6 shadow-sm sm:rounded-lg">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Checklist & Upload Dokumen Proposal</h3>
        <ul class="divide-y divide-gray-200">
            <li
                v-for="docType in documentTypes"
                :key="docType.id"
                class="py-4 space-y-3"
            >
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <span v-if="uploadedDocMap[docType.id]" class="text-green-600 font-bold text-lg">
                            ✅
                        </span>
                        <span v-else class="text-red-500 font-bold text-lg">
                            ❌
                        </span>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ docType.name }}</p>
                            <p v-if="docType.description" class="text-xs text-gray-500">
                                {{ docType.description }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <a
                            v-if="uploadedDocMap[docType.id]"
                            :href="route('files.proposal-document', uploadedDocMap[docType.id].id)"
                            target="_blank"
                            class="inline-flex items-center text-xs font-semibold text-indigo-600 hover:text-indigo-900"
                        >
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Unduh (v{{ uploadedDocMap[docType.id].version }})
                        </a>

                        <span
                            v-if="uploadedDocMap[docType.id]"
                            class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20"
                        >
                            Sudah Unggah
                        </span>
                        <span
                            v-else
                            class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10"
                        >
                            Belum Diunggah
                        </span>
                    </div>
                </div>

                <!-- FileUpload Component for each document type if proposal is editable -->
                <div v-if="isEditable" class="mt-2 pl-8">
                    <FileUpload
                        :proposal-id="proposalId"
                        :document-type-id="docType.id"
                    />
                </div>
            </li>
        </ul>
    </div>
</template>
