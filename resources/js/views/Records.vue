<template>
    <div class="container-fluid">
        <div class="pb-2">

            <h4 class="align-middle d-inline-block">Отчеты</h4>&nbsp;

            <button class="btn btn-success d-inline align-middle" data-toggle="modal" data-target="#modalCreate" @click="onOpenAddWindow">
                <i class="fas fa-plus-square"/>
                Добавить
            </button>

            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalDownload">
                <i class="fas fa-file-excel"/>
                Экспорт в Excel для табеля
            </button>

            <button type="button" class="btn btn-primary" @click="exportToExcel" :disabled="!mayFilter || isExport">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" v-if="isExport"/>
                <i class="fas fa-file-excel"/>
                Экспорт в Excel
            </button>
<!--
            <form class="form-inline d-none d-xl-inline-flex">
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
                    <input type="date" class="form-control date" v-model="filterForm.date">
                </div>
                <div class="form-check form-group form-check-inline ml-2">
                    <label class="form-check-label">Только мои отчеты</label>&nbsp;
                    <input type="checkbox" class="form-check-input" v-model="isSortMy">
                </div>

                <div class="btn-group ml-2">
                    <button class="btn btn-primary" @click.prevent="getAllRecords" :disabled="!mayFilter">Фильтровать</button>
                    <button class="btn btn-primary" @click.prevent="resetFilters" :disabled="!mayFilter">Сбросить</button>
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
                        <button class="btn btn-primary" @click.prevent="resetFilters" :disabled="!mayFilter">Сбросить</button>
                    </div>
                </form>
            </div>
            -->
        </div>

        <div class="table-responsive">

            <table class="table table-hover"><!--v-else-if="countRecords"-->
                <thead>
                <tr>
                    <th scope="col">
                        <dropdown-menu
                            v-model="dropdownStates.date"
                            :interactive="true"
                            :transition="'fade'"
                        >
                            <u>
                                Дата
                                <i class="fas" :class="{
                                    'fa-long-arrow-alt-up': sort.date === 'asc',
                                    'fa-long-arrow-alt-down': sort.date === 'desc'
                                }" v-if="sort.date"></i>
                            </u>
                            <div slot="dropdown">
                                <h6 class="dropdown-header">Сортировка</h6>
                                <button class="dropdown-item" @click="sort.date = 'asc'; getAllRecords()">
                                    По возрастанию
                                    <i class="fas fa-check" v-if="sort.date === 'asc'"/>
                                </button>
                                <button class="dropdown-item" @click="sort.date = 'desc'; getAllRecords()">
                                    По убыванию
                                    <i class="fas fa-check" v-if="sort.date === 'desc'"/>
                                </button>
                                <div class="dropdown-divider"/>
                                <h6 class="dropdown-header">Фильтр</h6>
                                <div class="container">
                                    <div class="form-group">
                                        <select class="form-control form-control-sm" v-model="dateFilterType" @change="getAllRecords">
                                            <option value="0">Дата</option>
                                            <option value="1">Другой период</option>
                                        </select>
                                    </div>
                                    <div class="form-group" v-if="dateFilterType === '0'">
                                        <input type="date" class="form-control form-control-sm" v-model="filterForm.date" @change="getAllRecords">
                                    </div>
                                    <div class="form-inline form-group input-group" v-if="dateFilterType === '1'" style="width: 300px">
                                        <input type="date" class="form-control form-control-sm" v-model="period.start" @change="getAllRecords">
                                        <input type="date" class="form-control form-control-sm" v-model="period.end" @change="getAllRecords">
                                    </div>
                                </div>
                                <button class="dropdown-item" @click="resetFilter('date')">Сбросить фильтр</button>
                            </div>
                        </dropdown-menu>
                    </th>
                    <th scope="col">
                        <dropdown-menu
                            v-model="dropdownStates.project"
                            :interactive="true"
                            :transition="'fade'"
                        >
                            <u>
                                Проект
                                <i class="fas" :class="{
                                    'fa-long-arrow-alt-up': sort.project === 'asc',
                                    'fa-long-arrow-alt-down': sort.project === 'desc'
                                }" v-if="sort.project"></i>
                            </u>
                            <div slot="dropdown">
                                <h6 class="dropdown-header">Сортировка</h6>
                                <button class="dropdown-item" @click="sort.project = 'asc'; getAllRecords()">
                                    По возрастанию
                                    <i class="fas fa-check" v-if="sort.project === 'asc'"/>
                                </button>
                                <button class="dropdown-item" @click="sort.project = 'desc'; getAllRecords()">
                                    По убыванию
                                    <i class="fas fa-check" v-if="sort.project === 'desc'"/>
                                </button>
                                <div class="dropdown-divider"/>
                                <h6 class="dropdown-header">Фильтр</h6>
                                <div class="container">
                                    <input type="text" class="form-control form-control-sm" v-model="filterForm.project" @change="getAllRecords">
                                </div>
                                <button class="dropdown-item mt-2" @click="resetFilter('project')">Сбросить фильтр</button>
                            </div>
                        </dropdown-menu>
                    </th>
                    <th scope="col">
                        <dropdown-menu
                            v-model="dropdownStates.name"
                            :interactive="true"
                            :transition="'fade'"
                        >
                            <u>
                                Сотрудник
                                <i class="fas" :class="{
                                    'fa-long-arrow-alt-up': sort.name === 'asc',
                                    'fa-long-arrow-alt-down': sort.name === 'desc'
                                }" v-if="sort.name"></i>
                            </u>
                            <div slot="dropdown">
                                <h6 class="dropdown-header">Сортировка</h6>
                                <button class="dropdown-item" @click="sort.name = 'asc'; getAllRecords()">
                                    По возрастанию
                                    <i class="fas fa-check" v-if="sort.name === 'asc'"/>
                                </button>
                                <button class="dropdown-item" @click="sort.name = 'desc'; getAllRecords()">
                                    По убыванию
                                    <i class="fas fa-check" v-if="sort.name === 'desc'"/>
                                </button>
                                <div class="dropdown-divider"/>
                                <h6 class="dropdown-header">Фильтр</h6>
                                <div class="container">
                                    <input type="text" class="form-control form-control-sm" v-model="filterForm.name" @change="getAllRecords">
                                </div>
                                <button class="dropdown-item mt-2" @click="resetFilter('name')">Сбросить фильтр</button>
                            </div>
                        </dropdown-menu>
                    </th>
                    <th scope="col">
                        <dropdown-menu
                            v-model="dropdownStates.hours"
                            :interactive="true"
                            :transition="'fade'"
                        >
                            <u>
                                Часы
                                <i class="fas" :class="{
                                    'fa-long-arrow-alt-up': sort.hours === 'asc',
                                    'fa-long-arrow-alt-down': sort.hours === 'desc'
                                }" v-if="sort.hours"></i>
                            </u>
                            <div slot="dropdown">
                                <h6 class="dropdown-header">Сортировка</h6>
                                <button class="dropdown-item" @click="sort.hours = 'asc'; getAllRecords()">
                                    По возрастанию
                                    <i class="fas fa-check" v-if="sort.hours === 'asc'"/>
                                </button>
                                <button class="dropdown-item" @click="sort.hours = 'desc'; getAllRecords()">
                                    По убыванию
                                    <i class="fas fa-check" v-if="sort.hours === 'desc'"/>
                                </button>
                                <div class="dropdown-divider"/>
                                <h6 class="dropdown-header">Фильтр</h6>
                                <div class="container">
                                    <input type="text" class="form-control form-control-sm" v-model="filterForm.hours" @change="getAllRecords">
                                </div>
                                <button class="dropdown-item mt-2" @click="resetFilter('hours')">Сбросить фильтр</button>
                            </div>
                        </dropdown-menu>
                    </th>
                    <th scope="col" class="d-none d-sm-table-cell">
                        <dropdown-menu
                            v-model="dropdownStates.description"
                            :interactive="true"
                            :transition="'fade'"
                        >
                            <u>Описание</u>
                            <div slot="dropdown">
                                <h6 class="dropdown-header">Фильтр</h6>
                                <div class="container">
                                    <input type="text" class="form-control form-control-sm" v-model="filterForm.description" @change="getAllRecords">
                                </div>
                                <button class="dropdown-item mt-2" @click="resetFilter('description')">Сбросить фильтр</button>
                            </div>
                        </dropdown-menu>
                    </th>
                    <th scope="col">
                        <dropdown-menu
                            v-model="dropdownStates.created_at"
                            :interactive="true"
                            :transition="'fade'"
                        >
                            <u>
                                Создано
                                <i class="fas" :class="{
                                    'fa-long-arrow-alt-up': sort.created_at === 'asc',
                                    'fa-long-arrow-alt-down': sort.created_at === 'desc'
                                }" v-if="sort.created_at"></i>
                            </u>
                            <div slot="dropdown">
                                <h6 class="dropdown-header">Сортировка</h6>
                                <button class="dropdown-item" @click="sort.created_at = 'asc'; getAllRecords()">
                                    По возрастанию
                                    <i class="fas fa-check" v-if="sort.created_at === 'asc'"/>
                                </button>
                                <button class="dropdown-item" @click="sort.created_at = 'desc'; getAllRecords()">
                                    По убыванию
                                    <i class="fas fa-check" v-if="sort.created_at === 'desc'"/>
                                </button>
                                <div class="dropdown-divider"/>
                                <h6 class="dropdown-header">Фильтр</h6>
                                <div class="container">
                                    <input type="date" class="form-control form-control-sm" v-model="filterForm.created_at" @change="getAllRecords">
                                </div>
                                <button class="dropdown-item mt-2" @click="resetFilter('created_at')">Сбросить фильтр</button>
                            </div>
                        </dropdown-menu>
                    </th>
                    <th scope="col">
                        <dropdown-menu
                            v-model="dropdownStates.updated_at"
                            :interactive="true"
                            :transition="'fade'"
                        >
                            <u>
                                Изменено
                                <i class="fas" :class="{
                                    'fa-long-arrow-alt-up': sort.updated_at === 'asc',
                                    'fa-long-arrow-alt-down': sort.updated_at === 'desc'
                                }" v-if="sort.updated_at"></i>
                            </u>
                            <div slot="dropdown">
                                <h6 class="dropdown-header">Сортировка</h6>
                                <button class="dropdown-item" @click="sort.updated_at = 'asc'; getAllRecords()">
                                    По возрастанию
                                    <i class="fas fa-check" v-if="sort.updated_at === 'asc'"/>
                                </button>
                                <button class="dropdown-item" @click="sort.updated_at = 'desc'; getAllRecords()">
                                    По убыванию
                                    <i class="fas fa-check" v-if="sort.updated_at === 'desc'"/>
                                </button>
                                <div class="dropdown-divider"/>
                                <h6 class="dropdown-header">Фильтр</h6>
                                <div class="container">
                                    <input type="date" class="form-control form-control-sm" v-model="filterForm.updated_at" @change="getAllRecords">
                                </div>
                                <button class="dropdown-item mt-2" @click="resetFilter('updated_at')">Сбросить фильтр</button>
                            </div>
                        </dropdown-menu>
                    </th>
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
                    <td nowrap>{{ record.created_at }}</td>
                    <td nowrap>{{ record.updated_at }}</td>
                    <td nowrap>
                        <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#modalShow" @click="onOpenShowWindow(recordById(record.id))">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-primary btn-sm d-inline" data-toggle="modal" data-target="#modalUpdate"
                                @click="onOpenEditWindow(record)">
                            <i class="fas fa-pen d-inline"></i>
                        </button>
                        <button class="btn btn-danger btn-sm d-inline" data-toggle="modal" data-target="#modalDelete" @click="removeId = record.id">
                            <i class="fas fa-trash"></i>
                        </button>
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
                                    <input type="date" class="form-control" v-model="newRecord.date" :class="{'is-invalid':
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
                                            <label class="font-weight-bold">Проект</label>
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
                                            <input type="number" min="0" max="99" class="form-control" v-model.trim="rec.hours" @input="sumHours" @change="validateHours(rec)">
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

        <div class="modal fade" id="modalDownload" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Скачивание отчета</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-row">
                                <div class="col-md-6 pb-3">
                                    <label class="font-weight-bold">Месяц</label>
                                    <select class="form-control" v-model="selectedMonth">
                                        <option v-for="(month, index) in months" :value="index">{{month}}</option>
                                    </select>
                                </div>
                                <div class="col-md-6 pb-3">
                                    <label class="font-weight-bold">Год</label>
                                    <select class="form-control" v-model="selectedYear">
                                        <option v-for="year in years">{{year}}</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                        <button type="button" class="btn btn-primary" @click="exportGeneralToExcel" :disabled="!mayFilter || isExportGeneral">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" v-if="isExportGeneral"/>
                            <i class="fas fa-download"/>
                            Экспортировать
                        </button>
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
import {mapActions, mapGetters} from 'vuex'
import ConfirmWindow from "../components/ConfirmWindow";
import {required} from "vuelidate/lib/validators";
import ProjectSelect from "../components/ProjectSelect";
import DropdownMenu from '@innologica/vue-dropdown-menu'

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
        ProjectSelect,
        DropdownMenu,
    },
    data() {
        return {
            qwe: 0,
            dateFilterType: '0',
            months: [
                'Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь',
            ],
            years: [2020],
            loading: true,
            loadingWindow: true,
            totalHours: 0,
            isMyRecords: false,
            selectedMonth: moment().month(),
            selectedYear: moment().year(),
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
                hours: null,
                date: null,
                period: null,
                description: null,
                created_at: null,
                updated_at: null,
            },
            isSortMy: false,
            sort: {
                name: null,
                project: null,
                date: null,
                hours: null,
                created_at: null,
                updated_at: null,
            },
            sortBehaviour: {
                name: 0,
                project: 0,
                date: 0,
                hours: 0,
                created_at: 0,
                updated_at: 0,
            },
            dropdownStates: {
                name: null,
                project: null,
                date: null,
                hours: null,
                description: null,
                created_at: null,
                updated_at: null,
            },
            period: {
                start: null,
                end: null
            },
            isExport: false,
            isExportGeneral: false,
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
        ...mapGetters([
            'allRecords',
            'allUsersList',
            'projectsList',
            'allUsersNotMe',
            'recordById',
            'countRecords',
            'projectFromListByIndex',
        ]),
        ...mapGetters({
            auth: 'auth/auth',
            user: 'auth/user',
        }),
        mayFilter(){
            return !this.loading
        },
        isMobile(){
            return document.documentElement.clientWidth < 960
        },
        isDatePeriod(){
            return (this.period.start || this.period.end) && this.dateFilterType === '1'
        },
    },
    methods: {
        ...mapActions(['getUsersList', 'getProjectsList', 'getRecords', 'updateRecord', 'createRecord', 'deleteRecord',]),
        async onOpenEditWindow(record){
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

            this.loadingWindow = false
        },
        async onOpenAddWindow(){
            this.loadingWindow = true

            await this.getProjectsList()
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
        async onOpenShowWindow(rec){
            this.infoRecord = rec
        },
        async editRecord(){
            if(this.$v.record.$invalid){
                this.$v.record.$touch()
                return
            }

            this.saving = true

            await this.updateRecord(this.record)

            this.saving = false
        },
        async addRecord(){
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
                            boss_id: 0
                        }
                        if (this.newRecord.isNewUsers) {
                            obj.user = this.newRecord.selectedUsers[i].user
                        }
                        record.push(obj)
                    }
                }
            }

            this.saving = true

            await this.createRecord(record)

            this.saving = false
        },
        async removeRecord(id){
            await this.deleteRecord(id)
        },
        isAddMeReset(){
            this.isAddMe = this.newRecord.isNewUsers ? this.isAddMe : false
        },
        calculateHours(rec){
            let timeA = moment(rec.timeA, 'hh:mm')
            let timeB = moment(rec.timeB, 'hh:mm')
            rec.hours = Number(timeB.diff(timeA, 'hours', true).toFixed(1))
        },
        sumHours(){
            this.totalHours = 0
            this.newRecord.times.forEach((item) => {
                this.totalHours += Number(item.hours)
            })
        },
        async getAllRecords(page = 1){
            this.loading = true

            await this.getRecords({
                page,
                name: this.isSortMy ? this.user.name : this.filterForm.name,
                project: this.filterForm.project,
                date: this.isDatePeriod ? this.period : this.filterForm.date,
                hours: this.filterForm.hours,
                description: this.filterForm.description,
                created_at: this.filterForm.created_at,
                updated_at: this.filterForm.updated_at,
                sort: this.sort
            })

            this.loading = false
        },
        async resetFilters(){
            this.setAll(this.filterForm, null);
            this.setAll(this.sort, null);
            this.setAll(this.sortBehaviour, 0);

            this.isSortMy = false

            await this.getAllRecords()
        },
        async resetFilter(filter){
            this.filterForm[filter] = null
            this.setAll(this.period, null)

            this.isSortMy = false

            await this.getAllRecords()
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
            }
        },
        getYears(){
            let year = 2021
            const nowYear = moment().year()
            while(year <= nowYear){
                this.years.push(year)
                year++
            }
        },
        validateHours(val){
            let num = +val.hours
            val.hours = num.toFixed(1)
            this.sumHours()
        },
        exportToExcel(){
            this.sort.created_at = 'desc'

            this.isExport = true

            axios.get('api/export', {
                params: {
                    name: this.isSortMy ? this.user.name : this.filterForm.name,
                    project: this.filterForm.project,
                    date: this.isDatePeriod ? this.period : this.filterForm.date,
                    hours: this.filterForm.hours,
                    description: this.filterForm.description,
                    sort: this.sort
                },
                responseType: 'blob',
            }).then(res => {
                fileDownload(res.data, 'Список отчетов - Worktime.xlsx')
                this.isExport = false
                this.sort.created_at = null
            })
        },
        exportGeneralToExcel(){
            this.isExportGeneral = true

            axios.get('api/exportGeneral', {
                params: {
                    year: this.selectedYear,
                    month: this.selectedMonth + 1
                },
                responseType: 'blob',
            }).then(res => {
                fileDownload(res.data, 'Список отчетов для табеля - Worktime.xlsx')
            }).catch((e) => {
                    Vue.$toast.open({
                        message: e.name + ": " + e.message,
                        type: 'error',
                    })
                })
            .finally(() => {
                this.isExportGeneral = false
            })
        },
    },
    async mounted(){

        await this.getAllRecords()

        this.getYears()
    }
}
</script>

<style scoped>
    .max-width{
        width: 100%
    }
    .date{
        width: 155px
    }
    .dropdown-item:focus,.dropdown-item:active, .close:focus,.close:active{
        outline: none !important;
        box-shadow: none;
    }
</style>
