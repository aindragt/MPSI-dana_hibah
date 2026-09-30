<script setup>
import { computed } from 'vue'

const props = defineProps({
    documentTypes: {
        type: Array,
        required: true,
    },
    uploadedDocuments: {
        type: Array,
        default: () => [],
    },
})

// Map latest uploaded document per document_type_id
const uploadedDocMap = computed(() => {
    const map = {}
    props.uploadedDocuments.forEach((doc) => {
        // Since documents might have versions, get highest version or existing
        if (!map[doc.document_type_id] || doc.version > map[doc.document_type_id].version) {
            map[doc.document_type_id] = doc
        }
    })
    return map
})
</script>

<template>
    <div class="bg-white p-6 shadow-sm sm:rounded-lg">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Checklist Kelengkapan Dokumen</h3>
        <ul class="divide-y divide-gray-200">
            <li
                v-for="docType in documentTypes"
                :key="docType.id"
                class="py-3 flex items-center justify-between"
            >
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

                <div>
                    <span
                        v-if="uploadedDocMap[docType.id]"
                        class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20"
                    >
                        Sudah Unggah (v{{ uploadedDocMap[docType.id].version }})
                    </span>
                    <span
                        v-else
                        class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10"
                    >
                        Belum Diunggah
                    </span>
                </div>
            </li>
        </ul>
    </div>
</template>
