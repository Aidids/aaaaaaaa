const sortFrontEnd = {
    data() {
        return {
            sortByNameOrder: 0,
        }
    },
    methods: {
        sortByName() {
            this.sortByNameOrder = (this.sortByNameOrder === 1) ? -1 : 1
        },

    }
}

export default sortFrontEnd;
