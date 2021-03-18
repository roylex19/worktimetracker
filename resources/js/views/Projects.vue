<template>
        <div class="container-fluid">
            <div class="pb-2">
                <h4 class="d-inline align-middle">Список проектов</h4>&nbsp;
                <button class="btn btn-success d-inline align-middle" data-toggle="modal" data-target="#modalCreate" @click="onOpenAddWindow">Добавить</button>

                <form class="form-inline d-none d-xl-inline-flex">
                    <div class="form-group ml-2">
                        <input type="text" class="form-control" v-model="filterForm.title" placeholder="Проект">
                    </div>

                    <div class="btn-group ml-2">
                        <button class="btn btn-primary" @click.prevent="getAllProjects" :disabled="!mayFilter">Фильтровать</button>
                        <button class="btn btn-primary" @click.prevent="resetFilter" :disabled="!mayFilter">Сбросить</button>
                    </div>
                </form>

                <div class="btn-group d-xl-none">
                    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Фильтрация
                    </button>
                    <form class="dropdown-menu dropdown-menu-right p-4">
                        <div class="form-group ml-2">
                            <label>Проект</label>&nbsp;
                            <input type="text" class="form-control" v-model="filterForm.title">
                        </div>

                        <div class="btn-group ml-2">
                            <button class="btn btn-primary" @click.prevent="getAllProjects" :disabled="!mayFilter">Фильтровать</button>
                            <button class="btn btn-primary" @click.prevent="resetFilter" :disabled="!mayFilter">Сбросить</button>
                        </div>
                    </form>
                </div>

            </div>

            <div class="table-responsive-sm">

                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col" @click="setSort('title')">Название
                                <i class="fas" :class="{'fa-long-arrow-alt-up': sort.title === 'asc', 'fa-long-arrow-alt-down': sort.title === 'desc'}" v-if="sort.title"></i>
                            </th>
                            <th scope="col" @click="setSort('created_at')">Создано
                                <i class="fas" :class="{'fa-long-arrow-alt-up': sort.created_at === 'asc', 'fa-long-arrow-alt-down': sort.created_at === 'desc'}" v-if="sort.created_at"></i>
                            </th>
                            <th scope="col" @click="setSort('updated_at')">Изменено
                                <i class="fas" :class="{'fa-long-arrow-alt-up': sort.updated_at === 'asc', 'fa-long-arrow-alt-down': sort.updated_at === 'desc'}" v-if="sort.updated_at"></i>
                            </th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody v-if="!loading">
                        <tr v-for="(project, index) in allProjects.data">
                            <td nowrap>{{ project.title }}</td>
                            <td nowrap>{{ project.created_at }}</td>
                            <td nowrap>{{ project.updated_at }}</td>
                            <td nowrap>
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalUpdate" @click="onOpenEditWindow(project)">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modalDelete" @click="removeId = project.id">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <h5 class="d-flex justify-content-center mt-5" v-if="!countProjects && !loading">Нет ни одной записи</h5>
            </div>

            <pagination :data="allProjects" @pagination-change-page="getAllProjects" :align="'center'" :limit="1">
                <span slot="prev-nav">Назад</span>
                <span slot="next-nav">Вперед</span>
            </pagination>

            <div class="modal fade" id="modalUpdate" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Изменение записи</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form @submit.prevent="editProject" id="formEdit">
                                <div class="form-row">
                                    <div class="col-md-12 mb-3">
                                        <label>Название</label>
                                        <input type="text" class="form-control" v-model="project.title" :class="{'is-invalid':
                                        ($v.project.title.$dirty && !$v.project.title.required) ||
                                        ($v.project.title.$dirty && !$v.project.title.minLength)}">
                                        <div class="invalid-feedback" v-if="$v.project.title.$dirty">
                                            Введите название проекта
                                        </div>
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
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Создание записи</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form @submit.prevent="addProject" id="formCreate">
                                <div class="form-row">
                                    <div class="col-md-12 mb-3">
                                        <label>Название</label>
                                        <input type="text" class="form-control" v-model="project.title" :class="{'is-invalid':
                                        ($v.project.title.$dirty && !$v.project.title.required) ||
                                        ($v.project.title.$dirty && !$v.project.title.minLength)}">
                                        <div class="invalid-feedback" v-if="$v.project.title.$dirty">
                                            Введите название проекта
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

            <confirm-window id="modalDelete" message="Вы уверены, что хотите удалить запись?" @onClose="removeProject(removeId)"/>
        </div>
</template>

<script>
import {mapGetters, mapActions} from 'vuex'
import ConfirmWindow from "../components/ConfirmWindow";
import {minLength, required} from "vuelidate/lib/validators";
export default {
    components:{
        ConfirmWindow,
    },
    data(){
        return{
            loading: true,
            saving: false,
            removeId: 0,
            project: {
                id: 0,
                title: ''
            },
            filterForm: {
                title: null
            },
            sort: {
                title: null,
                created_at: null,
                updated_at: null
            },
            sortBehaviour: {
                title: 0,
                created_at: 0,
                updated_at: 0
            },
        }
    },
    validations: {
        project: {
            title: {
                required,
                minLength: minLength(2)
            }
        }
    },
    computed: {
        ...mapGetters(['allProjects', 'countProjects']),
        mayFilter(){
            return !this.loading
        }
    },
    methods: {
        ...mapActions(['getProjects', 'updateProject', 'createProject', 'deleteProject']),
        async onOpenEditWindow(project){
            this.project.id = project.id
            this.project.title = project.title
        },
        async onOpenAddWindow(){
            this.project.title = ''
        },
        async editProject(){
            if(this.$v.$invalid){
                this.$v.$touch()
                return
            }

            this.saving = true
            await this.updateProject(this.project)
            this.saving = false
        },
        async addProject(){
            if(this.$v.$invalid){
                this.$v.$touch()
                return
            }

            this.saving = true
            await this.createProject(this.project)
            this.saving = false
        },
        async removeProject(id){
            await this.deleteProject(id)
        },
        async getAllProjects(page = 1){
            this.loading = true

            await this.getProjects({
                page,
                title: this.filterForm.title,
                sort: this.sort
            })

            this.loading = false
        },
        async resetFilter(){
            this.setAll(this.filterForm, null);
            this.setAll(this.sort, null);
            this.setAll(this.sortBehaviour, 0);

            await this.getAllProjects()
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

            await this.getAllProjects()
        },
        setAll(obj, val) {
            Object.keys(obj).forEach(k => obj[k] = val)
        }
    },
    async mounted(){
        await this.getAllProjects()
    }
}
</script>

<style scoped>

</style>
