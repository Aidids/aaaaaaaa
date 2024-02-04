<template>
    <ul class="pagination justify-content-center">
        <li class="page-item">
            <a href="javascript:void(0);" class="page-link" type="button" @click="onClickFirstPage" :class="isInFirstPage ? 'disabled' : ''">
                First
            </a>
        </li>
        <li class="page-item">
            <a href="javascript:void(0);" class="page-link" type="button" @click="onClickPreviousPage" :class="isInFirstPage ? 'disabled' : ''">
                Prev
            </a>
        </li>

        <li v-for="page in pages" :key="page.name" class="page-item">
            <a href="javascript:void(0);" type="button" @click="onClickPage(page.name)"
                    :disabled="page.isDisabled" :class="{ pageActive: isPageActive(page.name) }"
                    class="page-link">
                {{ page.name }}
            </a>
        </li>

        <li class="page-item">
            <a href="javascript:void(0);" class="page-link" type="button" @click="onClickNextPage" :class="isInLastPage ? 'disabled' : ''">
                Next
            </a>
        </li>
        <li class="page-item">
            <a href="javascript:void(0);" class="page-link" type="button" @click="onClickLastPage" :class="isInLastPage ? 'disabled' : ''">
                Last
            </a>
        </li>
    </ul>
</template>

<script>
export default {
    props: {
        maxVisibleButtons: {
            type: Number,
            required: false,
            default: 3
        },
        totalPages: {
            type: Number,
            required: true
        },
        currentPage: {
            type: Number,
            required: true
        },
        perPage: {
            type: Number,
            required: true
        },
    },
    computed: {
        startPage() {
            // When on the first page
            if (this.currentPage === 1) {
                return 1;
            }

            // When on the last page
            if (this.currentPage === this.totalPages) {
                const start = this.totalPages - (this.maxVisibleButtons - 1);

                if (start === 0) {
                    return 1;
                } else {
                    return start;
                }
            }

            // When inbetween
            return this.currentPage - 1;
        },
        pages() {
            const range = [];

            for (
                let i = this.startPage;
                i <= Math.min(this.startPage + this.maxVisibleButtons - 1, this.totalPages);
                i++
            ) {
                range.push({
                    name: i,
                    isDisabled: i === this.currentPage
                });
            }

            return range;
        },
        isInFirstPage() {
            return this.currentPage === 1;
        },
        isInLastPage() {
            return this.currentPage === this.totalPages;
        },
    },
    methods: {
        onClickFirstPage() {
            this.$emit('pagechanged', 1);
        },
        onClickPreviousPage() {
            this.$emit('pagechanged', this.currentPage - 1);
        },
        onClickPage(page) {
            this.$emit('pagechanged', page);
        },
        onClickNextPage() {
            this.$emit('pagechanged', this.currentPage + 1);
        },
        onClickLastPage() {
            this.$emit('pagechanged', this.totalPages);
        },
        isPageActive(page) {
            return this.currentPage === page;
        }
    }
};
</script>

<style>
.pagination {
    list-style-type: none;

}

.pagination-item {
    display: inline-block;
    float: left;
    color: #ffffff;
    cursor: pointer;
    border-radius: 25px;
    overflow: auto;
    background-color: transparent;

}

.pageActive {
    background-color: #069c63;
    color: #ffffff;
}
</style>
