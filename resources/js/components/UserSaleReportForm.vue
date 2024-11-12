<template>
   <form :action="action" class="flex w-1/2 gap-3 mb-6">
      <!-- Select Department -->
      <select name="department" class="select max-w-52 w-fit" v-model="selectedDepartmentId">
         <option disabled :selected="!selectedDepartmentId" value="">
            Выберите отдел
         </option>
         <option v-for="department in departments" :key="department.id" :value="department.id">
            {{ department.name }}
         </option>
      </select>

      <!-- Select Date -->
      <input type="month" name="date" class="border px-3 py-1" v-model="selectedDate">

      <!-- Select User -->
      <select name="user" class="select max-w-52 w-fit" v-model="selectedUser">
         <option disabled :selected="!selectedUser" value="">
            Выберите сотрудника
         </option>
         <option v-for="user in filtredUsers" :key="user.id" :value="user.id">
            {{ user.first_name }} {{ user.last_name }}
         </option>
      </select>

      <button type="submit" class="btn">Выбрать</button>
   </form>
</template>

<script>
export default {
   props: {
      departments: Array,
      users: Array,
      initialDepartment: Object,
      initialUser: Object,
      initialDate: String,
      action: String
   },
   data() {
      return {
         selectedDepartmentId: this.initialDepartment?.id || null,
         selectedDepartment: this.initialDepartment || null,
         selectedUser: this.initialUser?.id || "",
         selectedDate: this.initialDate || new Date().toISOString().slice(0, 7),
         filtredUsers: this.users,
      };
   },
   watch: {
      selectedDepartmentId(newId) {
         this.selectedDepartment = this.departments.find(dep => dep.id === newId) || null;
         this.filterUsers();
      }
   },
   mounted() {
      this.filterUsers();
   },
   methods: {
      filterUsers() {
         if (this.selectedDepartment.parent_id == null) {
            this.filtredUsers = this.users

         } else {
            this.filtredUsers = this.users.filter(
               user => user.department_id === this.selectedDepartment.id
            );
         }
      },
   }
};
</script>