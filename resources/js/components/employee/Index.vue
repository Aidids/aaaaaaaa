<template>
  <h4>All Employees</h4>
  <div class="input-search w-50 mb-4">
    <i class="fa fa-search text-secondary"></i>
    <input v-debounce:1000ms="search" type="text" placeholder="Search employee" class="form-control"/>
  </div>

  <loader v-if="this.searchLoad" :large="false" class="h-100 d-flex"/>
  <TableMain v-else>
    <thead>
    <tr>
      <th>Name</th>
      <th>Title</th>
      <th>Department</th>
      <th class="text-center">Action</th>
    </tr>
    </thead>
    <tbody>
    <tr v-for="user in users" :key="user.id">
      <td class="fw-bold">{{ user.name }}</td>
      <td>{{ user.title }}</td>
      <td>{{ user.department ?? 'No Department' }}</td>
      <td class="text-center">
        <button class="btn btn-success mt-2" @click="viewProfile(user.id)">View Profile</button>
        <button v-if="checkId" class="btn btn-success ms-lg-2 mt-2" @click="impersonate(user.id)">Impersonate</button>
      </td>
    </tr>
    </tbody>
  </TableMain>
  <Pagination
      :perPage="12"
      :totalPages="this.totalPages"
      :currentPage="this.currentPage"
      @pagechanged="onPageChange"
  />
</template>

<script>
import Pagination from "../elements/Pagination.vue"
import $api from "../api.js";
import TableMain from "../elements/TableMain.vue";
import SearchInput from "../elements/SearchInput.vue";


export default {
  props: ['user_id'],
  components: {Pagination, TableMain, SearchInput},
  data() {
    return {
      documentApi: '',
      showDepartment: false,
      totalPages: 1,
      currentPage: 1,
      pageLoad: true,
      searchLoad: false,
      documentData: {},
      isAdmin: false,
      deleteId: null,
      users: {},
    };
  },
  computed: {
    checkId() {
      return [186, 13, 2, 24].includes(parseInt(localStorage.getItem('user_id')))
    }
  },
  methods: {
    finishLoad() {
      setTimeout(() => this.pageLoad = false, 250);
    },
    finishSearchLoad() {
      setTimeout(() => this.searchLoad = false, 250);
    },
    viewProfile(id) {
      window.open(
          '/profile-settings/' + id,
          '_blank'
      );
    },
    impersonate(id) {
      window.open(
          '/impersonate/' + id,
          '_self/',
      );
    },
    onPageChange(page) {
      this.pageLoad = !this.pageLoad;
      this.currentPage = page;
      this.employeePagination();
    },
    async search(query) {
      this.searchLoad = true;
      await $api.get('/api/all-employees/' + query + '?page=1')
          .then(response => {
            this.users = response.data.data;
            this.totalPages = response.data.meta.last_page;
            this.finishSearchLoad();

          });
    },
    async getAllEmployeeApi() {
      await $api.get('/api/all-employees/')
          .then(response => {
            this.users = response.data.data;
            this.totalPages = response.data.meta.last_page;
            this.finishLoad();
          });
    },
    async employeePagination() {
      await $api.get('/api/all-employees/' + '?page=' + this.currentPage)
          .then(response => {
            this.users = response.data.data;
            this.totalPages = response.data.meta.last_page;
            this.finishLoad();
          });
    }
  },
  created() {
    this.getAllEmployeeApi();
  }


};

</script>


