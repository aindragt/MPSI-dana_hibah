export function useProposalStatus(status) {
    const statusMap = {
        draft: {
            label: 'Draft',
            badgeColor: 'bg-gray-100 text-gray-700',
            isEditable: true,
            isTerminal: false,
        },
        diajukan: {
            label: 'Diajukan',
            badgeColor: 'bg-blue-100 text-blue-800',
            isEditable: false,
            isTerminal: false,
        },
        verifikasi_online: {
            label: 'Verifikasi Berkas Online',
            badgeColor: 'bg-purple-100 text-purple-800',
            isEditable: false,
            isTerminal: false,
        },
        perlu_revisi: {
            label: 'Perlu Revisi',
            badgeColor: 'bg-yellow-100 text-yellow-800',
            isEditable: true,
            isTerminal: false,
        },
        menunggu_berkas_fisik: {
            label: 'Menunggu Berkas Fisik',
            badgeColor: 'bg-indigo-100 text-indigo-800',
            isEditable: false,
            isTerminal: false,
        },
        verifikasi_final: {
            label: 'Verifikasi Final',
            badgeColor: 'bg-green-100 text-green-800',
            isEditable: false,
            isTerminal: true,
        },
        ditolak: {
            label: 'Ditolak',
            badgeColor: 'bg-red-100 text-red-800',
            isEditable: false,
            isTerminal: true,
        },
    }

    const currentStatus = statusMap[status] || {
        label: status || 'Unknown',
        badgeColor: 'bg-gray-100 text-gray-700',
        isEditable: false,
        isTerminal: false,
    }

    return currentStatus
}
