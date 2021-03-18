<template>
    <div class="container-fluid">
        <div class="pb-2">
            <h4 class="d-inline align-middle">Список сотрудников</h4>&nbsp;
            <button class="btn btn-success d-inline align-middle" data-toggle="modal" data-target="#modalCreate" @click="onOpenAddWindow">Добавить</button>

            <form class="form-inline d-none d-xl-inline-flex">
                <div class="form-group ml-2">
                    <input type="text" class="form-control" v-model="filterForm.name" placeholder="ФИО">
                </div>
                <div class="form-group ml-2">
                    <input type="text" class="form-control" v-model="filterForm.department" placeholder="Отдел">
                </div>
                <div class="form-group ml-2">
                    <input type="text" class="form-control" v-model="filterForm.phone" placeholder="Телефон">
                </div>

                <div class="btn-group ml-2">
                    <button class="btn btn-primary" @click.prevent="getAllUsers" :disabled="!mayFilter">Фильтровать</button>
                    <button class="btn btn-primary" @click.prevent="resetFilter" :disabled="!mayFilter">Сбросить</button>
                </div>
            </form>

            <div class="btn-group d-xl-none">
                <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Фильтрация
                </button>
                <form class="dropdown-menu dropdown-menu-right p-4">
                    <div class="form-group ml-2">
                        <label>ФИО</label>&nbsp;
                        <input type="text" class="form-control" v-model="filterForm.name">
                    </div>
                    <div class="form-group ml-2">
                        <label>Отдел</label>&nbsp;
                        <input type="text" class="form-control" v-model="filterForm.department">
                    </div>
                    <div class="form-group ml-2">
                        <label>Телефон</label>&nbsp;
                        <input type="text" class="form-control" v-model="filterForm.phone">
                    </div>

                    <div class="btn-group ml-2">
                        <button class="btn btn-primary" @click.prevent="getAllUsers" :disabled="!mayFilter">Фильтровать</button>
                        <button class="btn btn-primary" @click.prevent="resetFilter" :disabled="!mayFilter">Сбросить</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive">

            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col" @click="setSort('name')">Имя
                            <i class="fas" :class="{
                                'fa-long-arrow-alt-up': sort.name === 'asc',
                                'fa-long-arrow-alt-down': sort.name === 'desc'
                            }" v-if="sort.name"></i>
                        </th>
                        <th scope="col" @click="setSort('department')">Отдел
                            <i class="fas" :class="{
                                'fa-long-arrow-alt-up': sort.department === 'asc',
                                'fa-long-arrow-alt-down': sort.department === 'desc'
                            }" v-if="sort.department"></i>
                        </th>
                        <th scope="col" @click="setSort('phone')">Номер
                            <i class="fas" :class="{
                                'fa-long-arrow-alt-up': sort.phone === 'asc',
                                'fa-long-arrow-alt-down': sort.phone === 'desc'
                            }" v-if="sort.phone"></i>
                        </th>
                        <th scope="col" @click="setSort('email')">Email
                            <i class="fas" :class="{
                                'fa-long-arrow-alt-up': sort.email === 'asc',
                                'fa-long-arrow-alt-down': sort.email === 'desc'
                            }" v-if="sort.email"></i>
                        </th>
                        <th scope="col" @click="setSort('created_at')">Создано
                            <i class="fas" :class="{
                                'fa-long-arrow-alt-up': sort.created_at === 'asc',
                                'fa-long-arrow-alt-down': sort.created_at === 'desc'
                            }" v-if="sort.created_at"></i>
                        </th>
                        <th scope="col" @click="setSort('updated_at')">Изменено
                            <i class="fas" :class="{
                                'fa-long-arrow-alt-up': sort.updated_at === 'asc',
                                'fa-long-arrow-alt-down': sort.updated_at === 'desc'
                            }" v-if="sort.updated_at"></i>
                        </th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody v-if="!loading">
                    <tr v-for="(user, index) in allUsers.data" :class="{'table-danger': user.disabled}">
                        <td nowrap>{{ user.name }} <span class="badge bg-info" v-if="user.admin">Администратор</span></td>
                        <td nowrap>{{ user.department !== null ? user.department.title : '' }}</td>
                        <td nowrap>{{ user.phone }}</td>
                        <td nowrap>{{ user.email }}</td>
                        <td nowrap>{{ user.created_at }}</td>
                        <td nowrap>{{ user.updated_at }}</td>
                        <td nowrap>
                            <button class="btn btn-primary btn-sm d-inline" data-toggle="modal" data-target="#modalUpdate" @click="onOpenEditWindow(user)">
                                <i class="fas fa-pen d-inline"></i>
                            </button>
                            <button class="btn btn-danger btn-sm d-inline" data-toggle="modal" data-target="#modalDelete" @click="removeId = user.id">
                                <i class="fas fa-trash"></i>
                            </button>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-cog"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <button class="dropdown-item" type="button" @click="openDepartmentsViewSetupWindow(user)">Видимость отделов</button>
                                    <button class="dropdown-item" type="button" @click="openPasswordSetupWindow(user.id)">Сбросить пароль</button>
                                    <button class="dropdown-item" type="button" @click="openAdminWindow(user)" data-toggle="modal" data-target="#modalAdminSetup">Настройка прав</button>
                                    <!--<button class="dropdown-item" type="button" @click="openDeactivateWindow(user.id)" data-toggle="modal" data-target="#modalDeactivate">Деактивировать</button>-->
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <h5 class="d-flex justify-content-center mt-5" v-if="!countUsers && !loading">Нет ни одной записи</h5>
        </div>

        <pagination :data="allUsers" @pagination-change-page="getAllUsers" :align="'center'" :limit="1">
            <span slot="prev-nav">Назад</span>
            <span slot="next-nav">Вперед</span>
        </pagination>

        <div class="modal fade" id="modalUpdate" tabindex="-1">
            <div class="modal-dialog">
                <loader v-if="loadingWindow"/>
                <div class="modal-content" v-else>
                    <div class="modal-header">
                        <h5 class="modal-title">Изменение записи</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="editUser" id="formEdit">
                            <div class="form-row">
                                <div class="col-md-12 mb-3">
                                    <label>Имя</label>
                                    <input type="text" class="form-control" v-model="user.name" :class="{'is-invalid':
                                    ($v.user.name.$dirty && !$v.user.name.required) ||
                                    ($v.user.name.$dirty && !$v.user.name.minLength)}">
                                    <div class="invalid-feedback" v-if="$v.user.name.$dirty">
                                        Введите ФИО
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-12 mb-3">
                                    <label>Отдел</label>
                                    <v-select placeholder="Выберите отдел"
                                              label="title"
                                              :options="departmentsList"
                                              v-model="user.department">
                                    </v-select>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-12 mb-3">
                                    <label>Номер</label>
                                    <input type="text" class="form-control" v-model="user.phone" :class="{'is-invalid':
                                       ($v.user.phone.$dirty && !$v.user.phone.phone)}">
                                    <div class="invalid-feedback" v-if="$v.user.phone.$dirty">
                                        Введите корректный номер телефона
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-12 mb-3">
                                    <label>Email</label>
                                    <input type="text" class="form-control" v-model="user.email" :class="{'is-invalid':
                                    ($v.user.email.$dirty && !$v.user.email.email)}">
                                    <div class="invalid-feedback" v-if="$v.user.email.$dirty">
                                        Введите корректный email
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-12">
                                    <label>Статус</label>
                                    <select class="form-control" v-model="user.disabled">
                                        <option :value="0">Активен</option>
                                        <option :value="1">Неактивен</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-primary" form="formEdit" :disabled="saving">Сохранить</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalCreate" tabindex="-1">
            <div class="modal-dialog">
                <loader v-if="loadingWindow"/>
                <div class="modal-content" v-else>
                    <div class="modal-header">
                        <h5 class="modal-title">Создание записи</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="addUser" id="formCreate">
                            <div class="form-row">
                                <div class="col-md-12 mb-3">
                                    <label>Имя</label>
                                    <input type="text" class="form-control" v-model="user.name" :class="{'is-invalid':
                                    ($v.user.name.$dirty && !$v.user.name.required) ||
                                    ($v.user.name.$dirty && !$v.user.name.minLength)}">
                                    <div class="invalid-feedback" v-if="$v.user.name.$dirty">
                                        Введите ФИО
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-12 mb-3">
                                    <label>Отдел</label>
                                    <v-select placeholder="Выберите отдел"
                                              label="title"
                                              :options="departmentsList"
                                              v-model="user.department">
                                    </v-select>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-12 mb-3">
                                    <label>Номер</label>
                                    <input type="text" class="form-control" v-model="user.phone" :class="{'is-invalid':
                                       ($v.user.phone.$dirty && !$v.user.phone.phone)}">
                                    <div class="invalid-feedback" v-if="$v.user.phone.$dirty">
                                        Введите корректный номер телефона
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-12 mb-3">
                                    <label>Email</label>
                                    <input type="text" class="form-control" v-model="user.email" :class="{'is-invalid':
                                    ($v.user.email.$dirty && !$v.user.email.email)}">
                                    <div class="invalid-feedback" v-if="$v.user.email.$dirty">
                                        Введите корректный email
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-primary" form="formCreate" :disabled="saving">Сохранить</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalDepartmentsViewSetup" tabindex="-1">
            <div class="modal-dialog">
                <loader v-if="loadingWindow"/>
                <div class="modal-content" v-else>
                    <div class="modal-header">
                        <h5 class="modal-title">Видимость отделов сотрудника</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Выберите отделы, которые будет видеть <em>{{user.name}}</em></p>
                        <div class="row mb-2" v-for="(el, index) in selectedDepartments">
                            <div class="col-10">
                                <v-select placeholder="Выберите отдел"
                                          label="title"
                                          :options="departmentsListNotMy(user.department.id)"
                                          v-model="el.department">
                                </v-select>
                            </div>
                            <div class="col-2 align-middle">
                                <button type="button" class="btn btn-dark btn-sm" @click="selectedDepartments.push({});" v-if="index === 0">
                                    <i class="fas fa-plus-square"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" v-if="selectedDepartments.length > 1 && index > 0" @click="selectedDepartments.splice(index, 1)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                        <button type="button" class="btn btn-primary" @click="addDepartmentView" :disabled="saving">Сохранить</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalPasswordSetup" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Смена пароля</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-group">
                                <label>Пароль</label>
                                <input type="password" class="form-control" v-model.trim="form.password" :class="{'is-invalid':
                    ($v.form.password.$dirty && !$v.form.password.required) ||
                    ($v.form.password.$dirty && !$v.form.password.minLength)}">
                                <div class="invalid-feedback" v-if="$v.form.password.$dirty">
                                    Длина пароля минимум 6 символов
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Подтверждение пароля</label>
                                <input type="password" class="form-control" v-model.trim="form.repeatPassword" :class="{'is-invalid':
                    ($v.form.repeatPassword.$dirty && !$v.form.repeatPassword.sameAsPassword)}">
                                <div class="invalid-feedback" v-if="$v.form.repeatPassword.$dirty">
                                    Пароли не совпадают
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                        <button type="button" class="btn btn-primary" :disabled="saving" @click.prevent="changePassword">Сохранить</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalAdminSetup" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Управление правами администратора</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-check form-check-inline">
                            <input type="checkbox" class="form-check-input" :disabled="saving" v-model="form.admin">
                            <label class="form-check-label">Права администратора</label>&nbsp;
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                        <button type="button" class="btn btn-primary" :disabled="saving" @click.prevent="changeAdminRule">Сохранить</button>
                    </div>
                </div>
            </div>
        </div>

        <confirm-window id="modalDelete" message="Вы уверены, что хотите удалить запись?" @onClose="removeUser(removeId)"/>

        <!--<confirm-window id="modalDeactivate" message="Вы уверены, что хотите деактивировать сотрудника?" @onClose="deactivateUser"/>-->
    </div>
</template>

<script>
import 'vue-select/dist/vue-select.css';
import vSelect from 'vue-select'
import {mapGetters, mapActions} from 'vuex'
import ConfirmWindow from "../components/ConfirmWindow";
import {minLength, required, email, numeric, maxLength, alpha, helpers, sameAs,} from "vuelidate/lib/validators";
import user from "../store/modules/user";
const phone = helpers.regex('phone', /^\+7\d{10}$/)
export default {
    components:{
        ConfirmWindow,
        vSelect
    },
    data(){
      return{
          loading: true,
          loadingWindow: true,
          saving: false,
          removeId: 0,
          user: {
              id: 0,
              name: '',
              phone: '',
              email: '',
              department: {},
              disabled: 0
          },
          selectedDepartments: [{}],
          form: {
              password: '',
              repeatPassword: '',
              admin: false
          },
          filterForm: {
              name: null,
              department: null,
              phone: null
          },
          sort: {
              name: null,
              department: null,
              phone: null,
              email: null,
              created_at: null,
              updated_at: null,
          },
          sortBehaviour: {
              name: 0,
              department: 0,
              phone: 0,
              email: 0,
              created_at: 0,
              updated_at: 0,
          },
      }
    },
    validations: {
        user: {
            name: {
                required,
                minLength: minLength(2)
            },
            phone: {
                phone
            },
            email: {
                email
            },
        },
        form: {
            password: {
                required,
                minLength: minLength(6),
            },
            repeatPassword: {
                sameAsPassword: sameAs('password')
            },
        },
        validationGroup: ['user', 'form']
    },
    computed: {
        ...mapGetters(['allUsersList', 'allUsers', 'allDepartments', 'departmentsList', 'departmentsListNotMy', 'allDepartmentsViews', 'countUsers']),
        mayFilter(){
            return !this.loading
        }
    },
    methods: {
        ...mapActions([
            'getUsersList',
            'getUsers',
            'getDepartments',
            'getDepartmentsList',
            'updateUser',
            'createUser',
            'deleteUser',
            'createDepartmentView',
            'getDepartmentsViews',
            'updatePassword',
            'updateAdminRule',
            'deactivateUser',
        ]),
        async onOpenEditWindow(user){
            this.loadingWindow = true

            this.user.id = user.id
            this.user.name = user.name
            this.user.phone = user.phone
            this.user.email = user.email
            this.user.department = user.department
            this.user.disabled = user.disabled

            await this.getDepartmentsList()

            this.loadingWindow = false
        },
        async onOpenAddWindow(){
            this.loadingWindow = true

            this.user.name = ''
            this.user.email = ''
            this.user.phone = ''
            this.user.department = {}

            await this.getDepartmentsList()

            this.loadingWindow = false
        },
        openAdminWindow(user){
            this.user.id = user.id
            this.form.admin = user.admin
        },
        openDeactivateWindow(id){
            this.user.id = id
        },
        async editUser(){
            if(this.$v.user.$invalid){
                this.$v.user.$touch()
                return
            }

            this.saving = true

            await this.updateUser(this.user)

            this.saving = false
        },
        async addUser(){
            if(this.$v.user.$invalid){
                this.$v.user.$touch()
                return
            }

            this.saving = true
            await this.createUser(this.user)
            this.saving = false
        },
        async removeUser(id){
            await this.deleteUser(id)
        },
        async openDepartmentsViewSetupWindow(user){
            this.user = user
            this.selectedDepartments = [{}]
            this.departmentsViewsForDelete = [{}]

            $('#modalDepartmentsViewSetup').modal('show')

            this.loadingWindow = true

            await this.getDepartmentsViews(user.id)
            await this.getDepartmentsList()

            if(this.allDepartmentsViews.length > 0) {
                this.selectedDepartments = this.allDepartmentsViews.map((val) => {
                    return {
                        department: val
                    }
                })
            }

            this.loadingWindow = false

        },
        async openPasswordSetupWindow(id){
            this.user.id = id
            this.form.password = ''
            this.form.repeatPassword = ''

            $('#modalPasswordSetup').modal('show')
        },
        async addDepartmentView(){

            this.saving = true

            await this.createDepartmentView({
                user: this.user,
                departments: this.selectedDepartments
            })

            this.saving = false
        },
        async changePassword(){
            if(this.$v.form.$invalid){
                this.$v.form.$touch()
                return
            }

            this.saving = true

            await this.updatePassword({
                id: this.user.id,
                password: this.form.password
            })

            this.saving = false
        },
        async deactivateUser(){
            await this.deactivateUser(this.user.id)
        },
        async changeAdminRule(){
            this.saving = true

            await this.updateAdminRule({
                id: this.user.id,
                admin: this.form.admin
            })

            this.saving = false
        },
        async getAllUsers(page = 1){
            this.loading = true

            await this.getUsers({
                page: page,
                name: this.filterForm.name,
                department: this.filterForm.department,
                phone: this.filterForm.phone,
                sort: this.sort
            })

            this.loading = false
        },
        async resetFilter(){
            this.setAll(this.filterForm, null);
            this.setAll(this.sort, null);
            this.setAll(this.sortBehaviour, 0);

            await this.getAllUsers()
        },
        async setSort(column){
            switch (this.sortBehaviour[column]) {
                case 0: {
                    this.sort[column] = 'asc'
                    this.sortBehaviour[column]++
                } break
                case 1: {
                    this.sort[column] = 'desc'
                    this.sortBehaviour[column]++
                } break
                case 2: {
                    this.sort[column] = null
                    this.sortBehaviour[column] = 0
                } break
            }

            await this.getAllUsers()
        },
        setAll(obj, val) {
            Object.keys(obj).forEach(k => obj[k] = val)
        }
    },
    async mounted(){
        await this.getAllUsers()
    },
}
</script>

<style scoped>
    .dropdown-toggle::after{
        content: none
    }
    .badge{
        color: #fff;
    }
</style>
