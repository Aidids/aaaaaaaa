export default {
    data() {
        return {
            fixedApproverStatus: [
                { label: 'All', current_approver: [0,179], status: ['processing', 'approved'] },
                { label: 'Processing', current_approver: [0], status: ['processing'] },
                { label: 'Approved by GM WMO', current_approver: [179], status: ['processing'] },
                { label: 'Approved by MD', current_approver: [0,179], status: ['approved'] },
            ],

            hrApprovalStatus: [
                { label: 'All', status: ['pending', 'approved', 'rejected', 'canceled', 'completed', 'expired'] },
                { label: 'Pending HR Approval', status: ['approved'] },
                { label: 'Completed', status: ['completed'] },
                { label: 'Pending', status: ['pending'] },
                { label: 'Rejected', status: ['rejected'] },
                { label: 'Canceled', status: ['canceled'] },
                { label: 'Expired', status: ['expired'] },
            ],

            standardStatus: [
                { label: 'All', status: ['pending', 'approved', 'rejected', 'canceled'] },
                { label: 'Approved', status: ['approved'] },
                { label: 'Pending', status: ['pending'] },
                { label: 'Rejected', status: ['rejected'] },
                { label: 'Canceled', status: ['canceled'] },
            ],

            total_hours: [
                {
                    value: 4,
                    label: '4 hours'
                },
                {
                    value: 8,
                    label: '8 hours'
                },
                {
                    value: 12,
                    label: '12 hours'
                },
            ],

            leave_type: [
                { label: 'All', type: [1,2,3,4,5,6,7,8,9,10,11] },
                { label: 'Annual Leave', type: [1] },
                { label: 'Medical Leave', type: [2] },
                { label: 'Hospitalisation Leave', type: [3] },
                { label: 'Unpaid Leave', type: [4] },
                { label: 'Emergency Leave', type: [5] },
                { label: 'Paternity Leave', type: [6] },
                { label: 'Maternity Leave', type: [7] },
                { label: 'Offshore Leave', type: [8] },
                { label: 'Replacement Leave', type: [9] },
                { label: 'Compassionate Leave', type: [10] },
                { label: 'Out of Office', type: [11] },
            ]
        }
    }
}
