<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import InputError from '@/Components/InputError.vue'

const props = defineProps({
    proposalId: {
        type: [Number, String],
        required: true,
    },
    documentTypeId: {
        type: [Number, String],
        required: true,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
})

const fileInput = ref(null)
const selectedFileName = ref('')
const isDragging = ref(false)

const form = useForm({
    document_type_id: props.documentTypeId,
    document: null,
})

const handleFileChange = (e) => {
    const file = e.target.files[0]
    if (file) {
        form.document = file
        selectedFileName.value = file.name
    }
}

const handleDrop = (e) => {
    isDragging.value = false
    if (props.disabled) return
    const file = e.dataTransfer.files[0]
    if (file) {
        form.document = file
        selectedFileName.value = file.name
    }
}

const triggerFileInput = () => {
    if (!props.disabled && fileInput.value) {
        fileInput.value.click()
    }
}

const uploadFile = () => {
    if (!form.document || props.disabled) return

    form.post(route('pengaju.proposals.documents.store', props.proposalId), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('document')
            selectedFileName.value = ''
            if (fileInput.value) fileInput.value.value = ''
        },
    })
}
</script>

<template>
    <div class="space-y-2">
        <div
            @dragover.prevent="isDragging = true"
            @dragleave.prevent="isDragging = false"
            @drop.prevent="handleDrop"
            :class="[
                'border-2 border-dashed rounded-lg p-3 text-center transition duration-150 ease-in-out cursor-pointer',
                isDragging ? 'border-indigo-500 bg-indigo-50' : 'border-gray-300 hover:border-indigo-400 bg-gray-50',
                disabled ? 'opacity-50 cursor-not-allowed' : '',
            ]"
            @click="triggerFileInput"
        >
            <input
                ref="fileInput"
                type="file"
                class="hidden"
                accept=".pdf,.jpg,.jpeg,.png"
                :disabled="disabled"
                @change="handleFileChange"
            />

            <div class="text-xs text-gray-600">
                <span v-if="selectedFileName" class="font-medium text-indigo-600">
                    {{ selectedFileName }}
                </span>
                <span v-else>
                    Klik atau drag & drop file (PDF, JPG, PNG maks 5MB)
                </span>
            </div>
        </div>

        <!-- Progress Indicator & Action Buttons -->
        <div v-if="form.document && !disabled" class="flex items-center justify-between text-xs">
            <span class="text-gray-500">File terpilih: {{ selectedFileName }}</span>
            <button
                type="button"
                @click.stop="uploadFile"
                :disabled="form.processing"
                class="inline-flex items-center px-2.5 py-1 bg-indigo-600 border border-transparent rounded font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition duration-150 ease-in-out"
            >
                <span v-if="form.processing">Mengunggah...</span>
                <span v-else>Unggah</span>
            </button>
        </div>

        <div v-if="form.progress" class="w-full bg-gray-200 rounded-full h-1.5 dark:bg-gray-700">
            <div
                class="bg-indigo-600 h-1.5 rounded-full transition-all duration-300"
                :style="{ width: form.progress.percentage + '%' }"
            ></div>
        </div>

        <InputError :message="form.errors.document" class="text-xs mt-1" />
    </div>
</template>
