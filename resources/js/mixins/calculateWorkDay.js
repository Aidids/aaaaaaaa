import setupCalendar from "./setupCalendar";

export default {
    mixins: [setupCalendar],

    data() {
        return {
            holidayDates: [],
            months: [
                'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
            ]
        }
    },

    created() {
        this.holidayDates = this.holidays.map(obj => new Date(obj.dates));
    },

    methods: {
        displayDate: function (date, dateType) {
            if (!date) {
                return 'Date not selected';
            }

            const [year, month, day] = date.split('-');
            const formattedMonth = this.months[parseInt(month) - 1]; // Adjust for 0-based indexing

            return `${parseInt(day)} ${formattedMonth} ${year}` + ` ${dateType ? '(' + dateType + ')' : ''}`;
        },

        convertDate: function (date) {
            let dateTime = new Date(date);
            let year = dateTime.getFullYear();
            let month = (dateTime.getMonth() + 1).toString().padStart(2, '0');
            let day = dateTime.getDate().toString().padStart(2, '0');

            return `${year}-${month}-${day}`;
        },

        convertMonthDate: function (date) {
            let dateTime = new Date(date);
            let year = dateTime.getFullYear();
            let month = this.months[dateTime.getMonth()];

            return `${month} ${year}`;
        },

        calculateDays(firstDate, secondDate) {
            let startDate = new Date(firstDate)
            let endDate = new Date(secondDate)
            const timeDifference = endDate.getTime() - startDate.getTime()
            return Math.floor(timeDifference / (1000 * 60 * 60 * 24) + 1)
        },

        calculateWorkDays(firstDate, secondDate, startDateType, endDateType) {
            if (!firstDate || !secondDate) {
                return '0';
            }

            let data = this.configureDateData(firstDate, secondDate, startDateType, endDateType);

            // Subtract two weekend days for every week in between
            let weeks = Math.floor(data.days / 7);
            data.days -= weeks * 2;

            // Handle special cases
            let startDay = data.startDate.getDay();
            let endDay = data.endDate.getDay();

            // Remove weekend not previously removed.
            if (startDay - endDay > 1) {
                data.days -= 2;
            }
            // Remove start day if span starts on Sunday but ends before Saturday
            if (startDay === 0 && endDay !== 6) {
                data.days--;
            }
            // Remove end day if span ends on Saturday but starts after Sunday
            if (endDay === 6 && startDay !== 0) {
                data.days--;
            }

            // filter holidays
            this.holidayDates.forEach(obj => {
                if ((obj >= data.startDate) && (obj <= data.endDate)) {
                    /* If it is not saturday (6) or sunday (0), subtract it */
                    if ( (obj.getDay() % 6) !== 0 )
                    {
                        data.days--;
                    }
                }
            });

            if (startDay === endDay) {
                data.days = this.calculateHalfDay(startDay, data.startDateType, data.days);
            } else {
                data.days = this.calculateHalfDay(startDay, data.startDateType, data.days);
                data.days = this.calculateHalfDay(endDay, data.endDateType, data.days);
            }

            return data.days;
        },

        /*calculate everyday : holiday, weekend, halfday*/
        calculateEveryDay(firstDate, secondDate, startDateType, endDateType) {
            if (!firstDate || !secondDate) {
                return '0';
            }

            let data = this.configureDateData(firstDate, secondDate, startDateType, endDateType);

            // Handle special cases
            let startDay = data.startDate.getDay();
            let endDay = data.endDate.getDay();

            if (startDay === endDay) {
                data.days = this.calculateHalfDay(startDay, data.startDateType, data.days, false);
            } else {
                data.days = this.calculateHalfDay(startDay, data.startDateType, data.days, false);
                data.days = this.calculateHalfDay(endDay, data.endDateType, data.days, false);
            }

            return data.days;
        },

        configureDateData(firstDate, secondDate, startDateType, endDateType) {
            let startDate = new Date(firstDate);
            let endDate = new Date(secondDate);
            let millisecondsPerDay = 86400 * 1000; // Day in milliseconds

            startDate.setHours(0, 0, 0, 1);
            endDate.setHours(23, 59, 59, 999);

            let diff = endDate - startDate;
            let days = Math.ceil(diff / millisecondsPerDay);

            return {
                startDate,
                endDate,
                startDateType,
                endDateType,
                days
            };
        },

        calculateHalfDay(day, dateType, days, ignoreWeekend = true) {
            // ignore if saturday or sunday
            if ( (day === 0 || day === 6) && ignoreWeekend ) {
                return days;
            }

            // subtract half if not full day
            if (dateType !== 'full day') {
                return days - 0.5;
            }

            return days;
        },

        annualLeaveWorkDay(d) {
            let date = new Date(d).getDay();

            switch (date) {
                // 3: Wednesday
                case 3:
                    return 5;
                // 4: Thursday
                case 4:
                    return 5;
                case 5:
                    return 5;
                case 6:
                    return 5;
                case 0:
                    return 4;
                default:
                    return 3;
            }
        },

        countCompassionateDay(d) {
            let date = new Date(d).getDay();

            switch (date) {
                case 4: // Thursday
                    return 4;
                case 5: // Friday
                    return 4;
                case 6: // Saturday
                    return 4;
                case 0: // Sunday
                    return 3;
                default:
                    return 2;
            }
        },

        compassionateWorkDay(userStartDate) {
            let days = this.countCompassionateDay(userStartDate);

            let duplicateDate = new Date(userStartDate);
            let dateApplyRange = this.compassionateDateRange(userStartDate, days);

            /*remove saturday sunday*/
            let filterWeekend = this.holidayDates.filter(date => (date.getDay() !== 6) && (date.getDay() !== 0));
            let compassionateHoliday = filterWeekend.map(date => date.setHours(0,0,0,0));

            /*loop through applied compassionate date with holiday add 1 day*/
            dateApplyRange.forEach((obj) => {
                let time = obj.setHours(0,0,0,0);
                if (compassionateHoliday.includes(time)) {
                    days++;
                }
            });

            /*new date range and check includes weekend*/
            let dateApplyWithHoliday = this.compassionateDateRange(duplicateDate, days);
            days += this.compassionateCheckWeekend(dateApplyWithHoliday);


            return days;
        },

        compassionateDateRange(start, days) {
            const dates = [];
            let currentDate = new Date(start);

            let predictEndDate = start.setDate(start.getDate() + days);

            while (currentDate.getTime() <= predictEndDate) {
                dates.push(new Date(currentDate));
                currentDate.setDate(currentDate.getDate() + 1);
            }

            return dates;
        },

        compassionateCheckWeekend(dateRange) {
            let last = dateRange[dateRange.length - 1];

            if (last.getDay() === 6 || last.getDay() === 0) {
                return 2;
            }

            return 0;
        },

        insufficientBalance(duration, balance, leaveTypeId) {
            let insufficient = false;
            if (balance !== undefined && duration !== undefined && leaveTypeId !== 10) {
                insufficient = balance < duration;
            }
            return insufficient;
        },

        setCalendarRange(startDate, endDate) {
            if (!startDate && !endDate) {
                return;
            }

            let selectedDate = {
                key: 'preview',
                highlight: {
                    start: {fillMode: "outline"},
                    base: {fillMode: "light"},
                    end: {fillMode: "outline"},
                },
                dates: {
                    start: new Date(startDate),
                    end: new Date(endDate),
                },
            };

            let temp = this.attributes[this.attributes.length - 1];

            if (temp.key === 'preview') {
                this.attributes.pop();
            }

            this.attributes.push(selectedDate);
        },

        calculateYearToDate(date) {
            if (date === null) {
                return 0
            }
            // Date in the format 'YYYY-MM-DD'
            const startDate = new Date(date);

            // Current date
            const currentDate = new Date();

            // Calculate the difference in milliseconds
            const timeDifference = currentDate - startDate;

            // Convert milliseconds to years
            const millisecondsInYear = 1000 * 60 * 60 * 24 * 365.25; // accounting for leap years
            const yearsDifference = timeDifference / millisecondsInYear;

            // Round to get the whole number of years
            return Math.floor(yearsDifference);
        }
    }
}
