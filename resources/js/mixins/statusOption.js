const statusOption = {
    methods: {
        getStatusOption(status) {
            return {
                'bg-secondary': status === 'pending',
                'bg-success': status === 'approved',
                'bg-warning': status === 'canceled',
                'bg-danger': status === 'rejected' || status === 'expired' || status === 'hr rejected',
                'bg-info':  status === 'completed' || status === 'processing',
            };
        },

        getStatusTextColor(status) {
            return {
                'text-secondary': status === 'pending',
                'text-success': status === 'approved',
                'text-warning': status === 'canceled',
                'text-danger': status === 'rejected' || status === 'expired' || status === 'hr rejected',
                'text-info':  status === 'completed' || status === 'processing',
            };
        },

        getApprovalOption(approval) {
            return {
                'pending': approval === 'pending' || approval === 'canceled',
                'approved': approval === 'approved',
                'rejected': approval === 'rejected',
            }
        },

        displayApproverBadge (request) {
            if (! request.current_approver)
            {
                return request.status;
            }

            if (request.current_approver.id === 179)
            {
                switch (request.status) {
                    case 'processing' : return `approved by GM WMO`
                    case 'approved' : return `${request.status} by MD`
                    default : return request.status;
                }
            }

            return request.status;
        }
    }
}

export default statusOption;
