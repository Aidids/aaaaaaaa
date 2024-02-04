export default {
    methods: {
        setOrdinalNumber(index) {
            switch (index) {
                case 1: return `${index}st `;
                case 2: return `${index}nd `;
                case 3: return `${index}rd `;
                default: return `${index}th `;
            }
        },

        formDataDate: function(date) {
            let dateTime = new Date(date);
            let year = dateTime.getFullYear();
            let month = (dateTime.getMonth() + 1).toString().padStart(2, '0');
            let day = dateTime.getDate().toString().padStart(2, '0');

            return `${year}-${month}-${day}`;
        },

        formatDateToMonth:function (dateString) {
            const months = [
                'January', 'February', 'March', 'April',
                'May', 'June', 'July', 'August',
                'September', 'October', 'November', 'December'
            ];

            const date = new Date(dateString);
            const monthIndex = date.getMonth();

            return months[monthIndex];
        },

        humanReadableDate: function(date) {
            if (date === null) {
                return 'No Date';
            }

            const inputDate = new Date(date);
            const day = inputDate.getDate();
            const month = inputDate.toLocaleString('default', { month: 'long' });
            const year = inputDate.getFullYear();

            return `${day} ${month} ${year}`;
        },

        halfMonthBackDated: function(date) {
            const inputDate = new Date(date);
            return new Date(inputDate.setDate(inputDate.getDate() - 15));
        },

        halfMonthForwardDated: function(date) {
            const inputDate = new Date(date);
            return new Date(inputDate.setDate(inputDate.getDate() + 46));
        },

        rawDaysBetweenDate: function(start_date, end_date) {
            if (start_date === null || end_date === null) {
                return 0;
            }
            const startDate = new Date(start_date);
            const startMillis = startDate.getTime();

            const endDate = new Date(end_date);
            const calcEndDate = endDate.setDate(endDate.getDate() + 1);
            const endMillis = new Date(calcEndDate).getTime();
            // Calculate the difference in milliseconds
            const millisDiff = endMillis - startMillis;
            // Convert milliseconds to days (1 day = 24 hours * 60 minutes * 60 seconds * 1000 milliseconds)
            return Math.floor(millisDiff / (24 * 60 * 60 * 1000));
        },

        leaveTypeColor(leaveType) {
            switch (leaveType) {
                case 'Annual Leave':
                    return 'blue';
                case 'Replacement Leave':
                    return 'blue';
                case 'Offshore Leave':
                    return 'blue';
                case 'Out Of Office Leave':
                    return 'blue';

                case 'Medical Leave':
                    return 'orange';
                case 'Emergency Leave':
                    return 'orange';
                case 'Hospitalisation Leave':
                    return 'orange';

                case 'Maternity Leave':
                    return 'teal';
                case 'Paternity Leave':
                    return 'teal';

                case 'Unpaid Leave':
                    return 'gray';
                case 'Compassionate Leave':
                    return 'gray';

                default: return 'gray';
            }
        },
    }
};
