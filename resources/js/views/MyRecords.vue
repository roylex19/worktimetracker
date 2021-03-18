<template>
    <div class="container-fluid">
        <div class="pb-2">
            <div class="row title">
                <h4 class="d-inline align-middle">
                    Отчеты
                    <small class="text-muted">{{user.department.title}}</small>
                </h4>&nbsp;
                <button class="btn btn-success d-inline align-middle" data-toggle="modal" data-target="#modalCreate" @click="onOpenAddWindow">Добавить</button>
            </div>

            <div class="row panel">
                <form class="form-inline d-none d-xl-inline-flex">
                    <div class="form-group ml-2">
                        <input type="text" class="form-control" v-model="filterForm.name" placeholder="Имя">
                    </div>
                    <div class="form-group ml-2">
                        <input type="text" class="form-control" v-model="filterForm.project" placeholder="Код проекта">
                    </div>
                    <div class="form-group ml-2">
                        <input type="date" class="form-control" v-model="filterForm.date">
                    </div>
                    <div class="form-check form-group form-check-inline ml-2">
                        <label class="form-check-label">Только мои отчеты</label>&nbsp;
                        <input type="checkbox" class="form-check-input" v-model="isSortMy">
                    </div>

                    <div class="btn-group">
                        <button class="btn btn-primary" @click.prevent="getAllRecords" :disabled="!mayFilter">Фильтровать</button>
                        <button class="btn btn-primary" @click.prevent="resetFilter" :disabled="!mayFilter">Сбросить</button>
                    </div>
                </form>

                <div class=" d-xl-none">
                    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Фильтрация
                    </button>
                    <form class="dropdown-menu dropdown-menu-right p-4">
                        <div class="form-group ml-2">
                            <label>ФИО</label>&nbsp;
                            <input type="text" class="form-control" v-model="filterForm.name">
                        </div>
                        <div class="form-group ml-2">
                            <label>Проект</label>&nbsp;
                            <input type="text" class="form-control" v-model="filterForm.project">
                        </div>
                        <div class="form-group ml-2">
                            <label>Дата</label>&nbsp;
                            <input type="date" class="form-control" v-model="filterForm.date">
                        </div>
                        <div class="form-check form-group form-check-inline ml-2">
                            <label class="form-check-label">Только мои отчеты</label>&nbsp;
                            <input type="checkbox" class="form-check-input" v-model="isSortMy">
                        </div>

                        <div class="btn-group ml-2">
                            <button class="btn btn-primary" @click.prevent="getAllRecords" :disabled="!mayFilter">Фильтровать</button>
                            <button class="btn btn-primary" @click.prevent="resetFilter" :disabled="!mayFilter">Сбросить</button>
                        </div>
                    </form>
                </div>

                <report-tracker style="margin-left: auto"></report-tracker>
            </div>
        </div>

        <div class="table-responsive">

            <table class="table table-hover">
                <thead>
                <tr>
                    <th scope="col" @click="setSort('date')">Дата
                        <i class="fas" :class="{'fa-long-arrow-alt-up': sort.date === 'asc', 'fa-long-arrow-alt-down': sort.date === 'desc'}" v-if="sort.date"></i>
                    </th>
                    <th scope="col" @click="setSort('project')">Проект
                        <i class="fas" :class="{'fa-long-arrow-alt-up': sort.project === 'asc', 'fa-long-arrow-alt-down': sort.project === 'desc'}" v-if="sort.project"></i>
                    </th>
                    <th scope="col" @click="setSort('name')">Сотрудник
                        <i class="fas" :class="{'fa-long-arrow-alt-up': sort.name === 'asc', 'fa-long-arrow-alt-down': sort.name === 'desc'}" v-if="sort.name"></i>
                    </th>
                    <th scope="col" @click="setSort('hours')">Часы
                        <i class="fas" :class="{'fa-long-arrow-alt-up': sort.hours === 'asc', 'fa-long-arrow-alt-down': sort.hours === 'desc'}" v-if="sort.hours"></i>
                    </th>
                    <th scope="col" class="d-none d-sm-table-cell">Описание</th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody v-if="!loading">
                    <tr v-for="(record, index) in allRecords.data">
                        <td nowrap>{{ record.date }}</td>
                        <td nowrap>{{ record.project.title }}</td>
                        <td nowrap>{{ record.user.name | shortName}}</td>
                        <td nowrap>{{ record.hours }}</td>
                        <td nowrap class="d-none d-sm-table-cell">{{ record.description | shortDescription }}</td>
                        <td nowrap>
                            <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#modalShow" @click="onOpenShowWindow(recordById(record.id))">
                                <i class="fas fa-eye"></i>
                            </button>
                            <span v-if="checkAbilityForModify(record.created_at) && (record.user.id === user.id) || (user.id === record.boss_id) || (user.admin)">
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalUpdate"
                                        @click="onOpenEditWindow(record)">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modalDelete" @click="removeId = record.id" v-if="(record.user.id === user.id) || (user.id === record.boss_id) || (user.admin)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>

            <h5 class="d-flex justify-content-center mt-5" v-if="!countRecords && !loading">Нет ни одной записи</h5>
        </div>

        <pagination :data="allRecords" @pagination-change-page="getAllRecords" :align="'center'" :limit="1">
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
                        <form>
                            <div class="form-row">
                                <div class="col-md-12 mb-3">
                                    <label class="font-weight-bold">Дата</label>
                                    <input type="date" class="form-control" v-model="record.date" :class="{'is-invalid':
                                    ($v.record.date.$dirty && !$v.record.date.required) ||
                                    ($v.record.date.$dirty && !$v.newRecord.date.twoLastDays)}">
                                    <div class="invalid-feedback" v-show="$v.record.date.$dirty && !$v.record.date.required">
                                        Выберите дату
                                    </div>
                                    <div class="invalid-feedback" v-show="$v.record.date.$dirty && !$v.record.date.twoLastDays">
                                        Дата отчета должна быть в диапазоне двух прошедших дней с момента создания отчета
                                    </div>
                                </div>
                            </div>
                            <div class="form-row" v-if="record.boss_id !== 0">
                                <div class="col-md-12 mb-3">
                                    <label class="font-weight-bold">Сотрудник</label>
                                    <v-select placeholder="Выберите сотрудника"
                                              :options="allUsersNotMe(user.id)"
                                              label="name"
                                              v-model="record.user" :class="{'is-invalid':
                                    ($v.record.user.$dirty && !$v.record.user.required)}">
                                    </v-select>
                                    <div class="invalid-feedback" v-if="$v.record.user.$dirty">
                                        Выберите дату
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-12 mb-3">
                                    <label class="font-weight-bold">Проект</label>
                                    <project-select :records="projectsList" :value="record.project" @input="record.project = $event"/>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-12 form-group">
                                    <label class="font-weight-bold">Время (ч.)</label>
                                    <input type="number" min="0" class="form-control" v-model.trim="record.hours" @change="validateHours(record)" :class="{'is-invalid':
                                    ($v.record.hours.$dirty && !$v.record.hours.required)}">
                                    <div class="invalid-feedback" v-if="$v.record.hours.$dirty">
                                        Введите часы работы
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-12 form-group">
                                    <label class="font-weight-bold">Описание</label>
                                    <textarea class="form-control" rows="6" v-model.trim="record.description"/>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                        <button type="button" class="btn btn-primary" @click="editRecord" :disabled="saving">Сохранить</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalCreate" tabindex="-1" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog">
                <loader v-if="loadingWindow"/>
                <div class="modal-content" v-else>
                    <div class="modal-header">
                        <h5 class="modal-title">Создание отчета</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-row">
                                <div class="col-md-12 pb-3">
                                    <label class="font-weight-bold">Дата</label>
                                    <input type="date" class="form-control date" v-model="newRecord.date" :class="{'is-invalid':
                                    ($v.newRecord.date.$dirty && !$v.newRecord.date.required) ||
                                    ($v.newRecord.date.$dirty && !$v.newRecord.date.twoLastDays)}">
                                    <div class="invalid-feedback" v-show="$v.newRecord.date.$dirty && !$v.newRecord.date.required">
                                        Выберите дату
                                    </div>
                                    <div class="invalid-feedback" v-show="$v.newRecord.date.$dirty && !$v.newRecord.date.twoLastDays">
                                        Создать отчет возможно только в течение прошлых двух дней
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-12 pb-3">
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">Добавить людей в отчет</label>&nbsp;
                                        <input type="checkbox" class="form-check-input" @click="isAddMeReset" v-model="newRecord.isNewUsers">
                                    </div>
                                    <div class="form-check form-check-inline" v-if="newRecord.isNewUsers">
                                        <label class="form-check-label">Добавить себя в отчет</label>&nbsp;
                                        <input type="checkbox" class="form-check-input" v-model="isAddMe">
                                    </div>
                                </div>
                            </div>
                            <div class="form-row" v-if="newRecord.isNewUsers">
                                <div class="col-md-12">
                                    <div class="row mb-2" v-for="(selectedUser, index) in newRecord.selectedUsers">
                                        <div class="col-10">
                                            <v-select placeholder="Выберите сотрудника"
                                                      :options="allUsersNotMe(user.id)"
                                                      label="name"
                                                      v-model="selectedUser.user">
                                            </v-select>
                                        </div>
                                        <div class="col-2 align-middle">
                                            <button type="button" class="btn btn-dark btn-sm" @click="newRecord.selectedUsers.push({});" v-if="index === 0">
                                                <i class="fas fa-plus-square"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm" v-if="newRecord.selectedUsers.length > 1 && index > 0" @click="newRecord.selectedUsers.splice(index, 1)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="" v-for="(rec, index) in newRecord.times">
                                <div class="card mb-2">
                                    <div class="card-body form-row">
                                        <div class="col-md-12">
                                            <span class="float-right" v-if="index > 0" @click="newRecord.times.length > 1 ? newRecord.times.splice(index, 1) : null">
                                                <i class="fas fa-times"></i>
                                            </span><!--
                                            <div class="form-check form-check-inline mb-2">
                                                <label class="form-check-label">Присвоить данную запись сотруднику</label>&nbsp;
                                                <input type="checkbox" class="form-check-input" v-model="rec.isAddUser">
                                            </div>-->
                                        </div>
                                        <!--<div class="col-md-12 form-group" v-if="rec.isAddUser">
                                            <label class="font-weight-bold">Сотрудник</label>
                                            <v-select placeholder="Выберите сотрудника"
                                                      :options="allUsersExceptMe(user.id)"
                                                      label="name"
                                                      v-model="rec.user">
                                            </v-select>
                                            <small class="form-text text-muted">Выберите сотрудника, для которого будут записаны проект, часы и описание, введенные в данном блоке</small>
                                        </div>-->
                                        <div class="col-md-12 form-group">
                                            <label class="font-weight-bold">Проект</label><!--
                                            <v-select placeholder="Выберите проект"
                                                      :options="projectsList"
                                                      label="title"
                                                      v-model="rec.project">
                                            </v-select>-->
                                            <project-select :records="projectsList" :value="rec.project" @input="rec.project = $event"/>
                                        </div>
                                        <div class="col-md-12 form-group">
                                            <label class="font-weight-bold">Время (ч.)</label>
                                            <div class="form-check form-check-inline float-right">
                                                <label class="form-check-label">Калькулятор часов</label>&nbsp;
                                                <input type="checkbox" class="form-check-input" v-model="rec.isCalculatedHours">
                                            </div>
                                            <div class="input-group mb-3" v-if="rec.isCalculatedHours">
                                                <input type="time" class="form-control" v-model="rec.timeA" @input="calculateHours(rec)" @change="sumHours">
                                                <input type="time" class="form-control" v-model="rec.timeB" @input="calculateHours(rec)" @change="sumHours">
                                                <small class="form-text text-muted">Калькулятор необходим для автоматического перевода в десятичное число сложных временных периодов, не более</small>
                                            </div>
                                            <input type="number" min="0" max="99" nonce class="form-control" v-model.trim="rec.hours" @input="sumHours" @change="validateHours(rec)">
                                        </div>
                                        <div class="col-md-12 form-group">
                                            <label class="font-weight-bold">Описание</label>
                                            <textarea class="form-control" rows="6" v-model.trim="rec.description"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-dark btn-Add" @click="newRecord.times.push({hours: 0, project: projectFromListByIndex()})">Добавить</button>
                            <span class="float-right">Сумма часов: <span class="font-weight-bold">{{+totalHours ? totalHours : 0}}</span></span>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                        <button type="button" class="btn btn-primary" @click="addRecord" :disabled="saving">Сохранить</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalShow" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Просмотр отчета</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <h6 class="mb-3">{{infoRecord.user.name}}</h6>
                        <p><span class="font-weight-bold">Дата: </span>{{infoRecord.date}}</p>
                        <p><span class="font-weight-bold">Проект: </span>{{infoRecord.project.title}}</p>
                        <p><span class="font-weight-bold">Часы: </span>{{infoRecord.hours}}</p>
                        <span class="font-weight-bold">Описание:</span>
                        <p>{{infoRecord.description}}</p>
                    </div>
                    <div class="modal-footer">
                        <span class="text-muted max-width">
                            <span class="float-left">Создано {{infoRecord.created_at}}</span>
                        </span>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                    </div>
                </div>
            </div>
        </div>

        <confirm-window id="modalDelete" message="Вы уверены, что хотите удалить запись?" @onClose="removeRecord(removeId)"/>
    </div>
</template>

<script>
import 'vue-select/dist/vue-select.css';
import vSelect from 'vue-select'
import {mapGetters, mapActions} from 'vuex'
import ConfirmWindow from "../components/ConfirmWindow";
import {minLength, numeric, required, requiredIf, helpers} from "vuelidate/lib/validators";
import ReportTracker from "../components/ReportTracker";
import ProjectSelect from "../components/ProjectSelect";

//const hoursRight = helpers.regex('hoursRight', /([0-9]+[\.\,]5)|([1-9]\d+)/)
const twoLastDays = (value) => {
    let format = "YYYY-MM-DD"
    let a = moment(value, format)
    let b = moment().format(format)
    return a.diff(b, 'days') > -3 && a.diff(b, 'days') <= 0
}
export default {
    components: {
        ConfirmWindow,
        vSelect,
        ReportTracker,
        ProjectSelect
    },
    data() {
        return {
            loading: true,
            loadingWindow: true,
            totalHours: 0,
            isMyRecords: false,
            daysToRestrict: 0,
            saving: false,
            removeId: 0,
            isAddMe: false,
            record: {
                id: 0,
                date: moment().format('YYYY-MM-DD'),
                user: {},
                project: {title: 'title'},
                hours: 0,
                description: '',
                boss_id: 0,
                created_at: null
            },
            newRecord: {
                boss_id: 0,
                isNewUsers: false,
                selectedUsers: [{}],
                date: moment().format('YYYY-MM-DD'),
                times: [{}],
            },
            infoRecord: {
                user: {},
                project: {},
            },
            filterForm: {
                name: null,
                project: null,
                date: null
            },
            isSortMy: false,
            sort: {
                name: null,
                project: null,
                date: null,
                hours: null
            },
            sortBehaviour: {
                name: 0,
                project: 0,
                date: 0,
                hours: 0
            },
            options: []
        }
    },
    validations: {
        record: {
            date: {
                required,
                //twoLastDays
            },
            project: {
                required
            },
            hours: {
                required,
            },
            user: {
                required
            }
        },
        newRecord: {
            date: {
                required,
                //twoLastDays
            },
        },
        validationGroup: ['record', 'newRecord']
    },
    computed: {
        ...mapGetters(['allRecords', 'allUsersList', 'projectsList', 'allUsersNotMe', 'recordById', 'countRecords', 'projectFromListByIndex']),
        ...mapGetters({
            auth: 'auth/auth',
            user: 'auth/user',
        }),
        mayFilter(){
            return !this.loading
        },
    },
    methods: {
        ...mapActions([
            'getUsersList',
            'getProjectsListRecent',
            'getProjectsList',
            'getRecordsOfDepartments',
            'updateRecord',
            'createRecord',
            'deleteRecord',
        ]),
        async onOpenEditWindow(record) {
            this.loadingWindow = true

            await this.getProjectsList()
            await this.getUsersList()

            this.record.id = record.id
            this.record.date = moment(record.date, 'DD.MM.YYYY').format('YYYY-MM-DD')
            this.record.project = record.project
            this.record.user = record.user
            this.record.hours = record.hours
            this.record.description = record.description
            this.record.boss_id = record.boss_id
            this.record.created_at = record.created_at

            this.loadingWindow = false
        },
        async onOpenAddWindow() {
            this.loadingWindow = true

            await this.getProjectsListRecent()
            await this.getUsersList()

            this.newRecord.boss_id = 0
            this.newRecord.isNewUsers = false
            this.newRecord.selectedUsers = [{}]
            this.newRecord.date = moment().format('YYYY-MM-DD')
            this.newRecord.times = [{
                hours: 0,
                project: this.projectFromListByIndex()
            }]
            this.totalHours = 0

            this.loadingWindow = false
        },
        async onOpenShowWindow(rec) {
            this.infoRecord = rec
        },
        async editRecord() {
            if (this.$v.record.$invalid) {
                this.$v.record.$touch()
                return
            }

            this.saving = true

            await this.updateRecord(this.record)

            this.saving = false
        },
        async addRecord() {
            if (this.$v.newRecord.$invalid) {
                this.$v.newRecord.$touch()
                return
            }

            let record = []

            if (!this.newRecord.isNewUsers || this.isAddMe) {
                for (let j = 0; j < this.newRecord.times.length; j++) {
                    if (Object.keys(this.newRecord.times[j]).length === 1)
                        continue
                    if (!this.validateField(this.newRecord.times[j].project, 'project'))
                        return
                    if (!this.validateField(this.newRecord.times[j].hours, 'hours'))
                        return
                    record.push({
                        date: this.newRecord.date,
                        description: this.newRecord.times[j].description,
                        hours: this.newRecord.times[j].hours,
                        project: this.newRecord.times[j].project,
                        user: this.user,
                        boss_id: 0
                    })
                }
            }

            if (this.newRecord.isNewUsers) {
                for (let i = 0; i < this.newRecord.selectedUsers.length; i++) {
                    if (!this.validateField(this.newRecord.selectedUsers[i].user, 'users'))
                        return
                    if (Object.keys(this.newRecord.selectedUsers[i]).length === 0)
                        continue
                    for (let j = 0; j < this.newRecord.times.length; j++) {
                        if (!this.validateField(this.newRecord.times[j].project, 'project'))
                            return
                        if (!this.validateField(this.newRecord.times[j].hours, 'hours'))
                            return
                        let obj = {
                            date: this.newRecord.date,
                            description: this.newRecord.times[j].description,
                            hours: this.newRecord.times[j].hours,
                            project: this.newRecord.times[j].project,
                            user: {},
                            boss_id: this.user.id
                        }
                        if (this.newRecord.isNewUsers) {
                            obj.user = this.newRecord.selectedUsers[i].user
                        }
                        record.push(obj)
                    }
                }
            }

            if (!this.validateField(this.totalHours, 'totalHours'))
                return

            this.saving = true

            await this.createRecord(record)

            this.saving = false
        },
        async removeRecord(id) {
            await this.deleteRecord(id)
        },
        isAddMeReset() {
            this.isAddMe = this.newRecord.isNewUsers ? this.isAddMe : false
        },
        calculateHours(rec) {
            let timeA = moment(rec.timeA, 'hh:mm')
            let timeB = moment(rec.timeB, 'hh:mm')
            rec.hours = Number(timeB.diff(timeA, 'hours', true).toFixed(1))
        },
        sumHours() {
            this.totalHours = 0
            this.newRecord.times.forEach((item) => {
                this.totalHours += Number(item.hours)
            })
        },
        checkAbilityForModify(value) {
            let format = "DD.MM.YYYY hh:mm"
            let a = moment(value, 'DD.MM.YYYY')
            let b = moment()
            return a.diff(b, 'days') >= -2
        },
        async getAllRecords(page = 1) {
            this.loading = true

            await this.getRecordsOfDepartments({
                id: this.user.id,
                page,
                name: this.isSortMy ? this.user.name : this.filterForm.name,
                project: this.filterForm.project,
                date: this.filterForm.date,
                sort: this.sort
            })

            this.loading = false
        },
        async resetFilter() {
            this.setAll(this.filterForm, null);
            this.setAll(this.sort, null);
            this.setAll(this.sortBehaviour, 0);

            this.isSortMy = false

            await this.getAllRecords()
        },
        async setSort(column) {
            switch (this.sortBehaviour[column]) {
                case 0: {
                    this.sort[column] = 'asc'
                    this.sortBehaviour[column]++
                }
                    break
                case 1: {
                    this.sort[column] = 'desc'
                    this.sortBehaviour[column]++
                }
                    break
                case 2: {
                    this.sort[column] = null
                    this.sortBehaviour[column] = 0
                }
                    break
            }

            await this.getAllRecords()
        },
        setAll(obj, val) {
            Object.keys(obj).forEach(k => obj[k] = val)
        },
        validateField(val, type) {
            if (type === 'project') {
                if(!val) {
                    Vue.$toast.open({
                        message: 'Поле с проектом не может быть пустым!',
                        type: 'error',
                    })
                    return false
                }
                return true
            } else if (type === 'hours') {
                if(!val) {
                    Vue.$toast.open({
                        message: 'Поле с часами не может быть пустым!',
                        type: 'error',
                    })
                    return false
                }
                else if(val < 0){
                    Vue.$toast.open({
                        message: 'Часы не могут быть отрицательными!',
                        type: 'error',
                    })
                    return false
                }
                else if(+val === 0){
                    Vue.$toast.open({
                        message: 'Часы должны быть больше 0!',
                        type: 'error',
                    })
                    return false
                }
                return true
            } else if (type === 'users') {
                if(!val) {
                    Vue.$toast.open({
                        message: 'Поле с сотрудником не может быть пустым!',
                        type: 'error',
                    })
                    return false
                }
                return true
            } else if (type === 'totalHours'){
                if(+val > 24){
                    Vue.$toast.open({
                        message: 'Сумма часов должна быть не больше 24!',
                        type: 'error',
                    })
                    return false
                }
                return true
            }
        },
        validateHours(val){
            let num = +val.hours
            val.hours = num.toFixed(1)
            this.sumHours()
        }
    },
    async mounted(){
        await this.getAllRecords()
    }
}
</script>

<style scoped>
    .max-width{
        width: 100%
    }

    .panel{
        padding: 5px 10px;
    }

    .title{
        padding-left: 10px;
    }

</style>
