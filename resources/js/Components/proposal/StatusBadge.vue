<script setup>
import { computed } from 'vue'
import { useProposalStatus } from '@/Composables/useProposalStatus'

const props = defineProps({
    status: {
        type: String,
        required: true,
    },
})

const statusInfo = computed(() => useProposalStatus(props.status))
</script>

<template>
    <span
        :class="[
            'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
            statusInfo.badgeColor,
        ]"
    >
        {{ statusInfo.label }}
    </span>
</template>
