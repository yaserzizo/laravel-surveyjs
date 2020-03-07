<template>
    <div>
        <v-toolbar>
            <v-btn icon class="mb-3" @click.native = "$router.push({name: 'home'})">
                <v-icon large>home</v-icon>
            </v-btn>
            <v-toolbar-title>Quiz #{{surveyId}} Results</v-toolbar-title>
        </v-toolbar>
        <v-data-table
                :expanded="expanded"
                show-expand
                :headers="headers"
                :items="results"
                :loading="loading"
                item-key="id"
                hide-actions
                class="elevation-1"
                @click:row="clicked"
                expand
        >

            <template slot="items" slot-scope="props">
                    <td class="text-sm-left">{{ props.item.id }}</td>
                    <td class="text-sm-left">{{ props.item.patient_name }}</td>
                    <td class="text-sm-left">{{ props.item.score }}</td>
                <td class="text-sm-left">{{ time_convert(props.item.time) }}</td>
                    <td class="text-sm-left">{{ props.item.passed==1?'True':'FALSE' }}</td>
                    <td class="text-sm-left">{{ props.item.created_at }}</td>
                <td class="text-sm-left" >
                    <v-btn slot="activator" color="primary" dark @click.native.stop="props.expanded = !props.expanded">{{props.expanded?'-':'+'}}</v-btn>
                </td>
                    <td class="text-sm-left layout px-0">
                        <v-btn slot="activator" color="primary" dark @click.native.stop="showSurvey(props.item)">Show in Quiz</v-btn>
                    </td>


            </template>

            <template slot="expand" slot-scope="props">
                <v-data-table
                        :expanded="expanded"
                        show-expand
                        :headers="subheaders"
                        :items="props.item.questions"
                        :loading="loading"
                        item-key="id"
                        hide-actions
                        class="elevation-1"
                        @click:row="clicked"
                        expand
                >

                    <template slot="items" slot-scope="props">
                        <td class="text-sm-left">{{ props.item.name }}</td>
                        <td class="text-sm-left">{{ time_convert(props.item.time_spent )}}</td>
                        <td class="text-sm-left">{{ props.item.score }}</td>
                        <td class="text-sm-left">{{ props.item.actual_score }}</td>

                    </template>
                </v-data-table>
                <!--<td class="text-sm-left">{{ props.item.patient_name }}</td>
                <td class="text-sm-left">{{ props.item.created_at }}</td>-->
            </template>
         <!--   <template slot="items" slot-scope="props">
                    <td class="text-sm-left">{{ props.item.id }}</td>
                    <td class="text-sm-left">{{ props.item.patient_name }}</td>
                <td class="text-sm-left">{{ props.item.score }}</td>
                <td class="text-sm-left">{{ props.item.passed }}</td>
                    <td class="text-sm-left">{{ props.item.created_at}}</td>
                    <td class="text-sm-left layout px-0">
                        <v-btn slot="activator" color="primary" dark @click.native.stop="showSurvey(props.item)">Show in Quiz</v-btn>
                    </td>
                <tr @click="props.expanded = !props.expanded">
            </template>-->
            <template slot="no-data">
                <v-alert :value="!results.length > 0" color="error" icon="warning">
                    Sorry, nothing to display here :(
                </v-alert>
                <v-btn color="primary" @click="getSurveyResults">Reset</v-btn>
            </template>
        </v-data-table>
        <div class="text-xs-center pt-2">
            <v-pagination v-model="page" :length="pageLength" :total-visible="7"></v-pagination>
        </div>

        <v-dialog v-model="dialog" fullscreen hide-overlay transition="dialog-bottom-transition">
            <v-card>
                <v-toolbar dark color="primary">
                    <v-btn icon dark @click.native="dialog = false">
                        <v-icon>close</v-icon>
                    </v-btn>
                    <v-toolbar-title>Show in Quiz</v-toolbar-title>
                    <v-spacer></v-spacer>
                </v-toolbar>
                <survey :survey="surveyData" v-if="Object.keys(surveyData).length"></survey>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
    import * as SurveyVue from 'survey-vue'
    import * as showdown from "showdown/dist/showdown.min";

    const Survey = SurveyVue.Survey
    SurveyVue.StylesManager.applyTheme(SurveyConfig.theme)

    import * as widgets from "surveyjs-widgets";

    Object.filter = (obj, predicate) =>
        Object.keys(obj)
            .filter( key => predicate(obj[key]) )
            .reduce( (res, key) => Object.assign(res, { [key]: obj[key] }), {} );

    const widgetsList = Object.filter(SurveyConfig.widgets, widget => widget === true);

    Object.keys(widgetsList).forEach(function (widget) {
        widgets[widget](SurveyVue);
    });

    export default {
        name: 'survey-result',
        components: {
            Survey
        },
        data () {
            return {
                expanded: [],
                singleExpand: false,
                results: [],
                loading: false,
                survey: {},
                dialog: false,
                surveyData: {},
                surveyId: this.$route.params.id,
                page: 1,
                pageLength: 1,
                headers: [
                    {
                        text: 'ID',
                        alignt: 'left',
                        value: 'id',
                        sortable: false
                    },
                    {
                        text: 'PATIENT',
                        value: 'patient_name',
                        sortable: true
                    },
                    {
                        text: 'SCORE',
                        value: 'score',
                        sortable: true
                    },
                    {
                        text: 'Time',
                        value: 'time',
                        sortable: true
                    },
                    {
                        text: 'PASSED',
                        value: 'passed',
                        sortable: true
                    },
                    {
                        text: 'Created date',
                        valie: 'created_at',
                        sortable: false
                    },
                    {
                        text: 'Actions',
                        value: 'actions',
                        sortable: false
                    }
                ],
                subheaders: [
                    {
                        text: 'name',
                        alignt: 'left',
                        value: 'name',
                        sortable: true
                    },
                    {
                        text: 'Time',
                        value: 'time_spent',
                        sortable: true
                    },
                    {
                        text: 'SCORE',
                        value: 'score',
                        sortable: true
                    },
                    {
                        text: 'Current Score',
                        value: 'actual_score',
                        sortable: true
                    }
                ],
            }
        },
        mounted () {
            this.getSurveyResults(this.$route.params.id);
        },
        watch: {
            page() {
                this.getSurveyResults();
            }
        },
        methods: {
            clicked(value) {
                console.log('clicked')
                console.log(value)

                this.expanded.push(value)
            },
             time_convert(num)
    {
        var hours = Math.floor(num / 60);
        var minutes = num % 60;
        return hours + ":" + minutes;
    },
            getSurveyResults(id = this.surveyId) {
                this.loading = true;
                axios.get('/survey/' + id + '/result?page=' + this.page)
                    .then((response) => {
                        this.results = response.data.data;
                        this.survey = response.data.meta.survey;
                        this.pageLength = Math.ceil(response.data.meta.total / response.data.meta.per_page);
                        this.loading = false;
                        this.surveyData = new SurveyVue.Model(response.data.meta.survey.json);
                        var converter = new showdown.Converter();
                        this.surveyData
                            .onTextMarkdown
                            .add(function (survey, options) {
                                //convert the mardown text to html
                                var str = converter.makeHtml(options.text);

                                //remove root paragraphs <p></p>
                                // str = str.substring(3);
                                //  str = str.substring(0, str.length - 4);
                                //set html
                                options.html = str;
                            });
                        this.surveyData.mode = 'display';

                    })
                    .catch((error) => {
                        console.info(error.response);
                        this.loading = false;
                    });
            },
            showSurvey(result) {
                this.dialog = true;
                this.surveyData.data = result.json;
               }
        }
    }
</script>
<style scoped>
    table.table {
        margin:0 auto;
        width: 98%;
        max-width: 98%;
    }

    .datatable-cell-wrapper {
        width: inherit;
        position: relative;
        z-index: 4;
        padding: 10px 24px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .datatable__expand-content .card__text {
        z-index: 3;
        position: relative;
    }
    .datatable-container {
        position: absolute;
        background-color: white;
        top: -50px;
        left: -14px;
        right: -14px;
        bottom: 0;
        z-index: 2;
        border:1px solid #ccc;
        box-shadow: 0 4px 5px 0 rgba(0,0,0,0.15), 0 1px 10px 0 rgba(0,0,0,0.15), 0 2px 4px -1px rgba(0,0,0,0.2);
    }
</style>
