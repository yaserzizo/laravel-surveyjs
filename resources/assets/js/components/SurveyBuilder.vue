<template>
    <div>
        <div id="surveyEditorContainer">
        </div>
    </div>
</template>


<script>
    import * as SurveyCreator from 'survey-creator'
    import 'survey-creator/survey-creator.css';

    import * as SurveyKo from "survey-knockout";
    import * as widgets from "surveyjs-widgets";
    import * as SurveyVue from "survey-vue";

    Object.filter = (obj, predicate) =>
        Object.keys(obj)
            .filter( key => predicate(obj[key]) )
            .reduce( (res, key) => Object.assign(res, { [key]: obj[key] }), {} );

    const widgetsList = Object.filter(SurveyConfig.widgets, widget => widget === true);
    CKEDITOR.config.contentsLangDirection = 'rtl';
    CKEDITOR.config.contentsLanguage = 'ar';
    CKEDITOR.config.language = 'ar';
    Object.keys(widgetsList).forEach(function (widget) {
        widgets[widget](SurveyKo);
    });

    export default {
        name: 'survey-builder',
        props: ['json', 'id'],
        data () {
            return {
                surveyData: this.json,
                surveyId: this.id
            }
        },
        mounted () {
            let editorOptions = SurveyConfig.builder;
            SurveyCreator.StylesManager.applyTheme(SurveyConfig.builder.theme);
            var CkEditor_ModalEditor = {
                afterRender: function(modalEditor, htmlElement) {
                    var editor = CKEDITOR.replace(htmlElement);
                    editor.on('change', function() {
                        modalEditor.editingValue = editor.getData();
                    });
                    editor.setData(modalEditor.editingValue);
                },
                destroy: function(modalEditor, htmlElement) {
                    var instance = CKEDITOR.instances[htmlElement.id];
                    if (instance){
                        instance.removeAllListeners();
                        CKEDITOR.remove(instance);
                    }
                }
            };
            SurveyCreator.SurveyPropertyModalEditor.registerCustomWidget("html", CkEditor_ModalEditor);

            this.editor = new SurveyCreator.SurveyCreator('surveyEditorContainer', editorOptions);
            this.editor.showToolbox = "right";
//Show property grid in the right container, combined with toolbox
            this.editor.showPropertyGrid = "right";
//Make toolbox active by default
            this.editor.rightContainerActiveItem("toolbox");
            this.editor.text = JSON.stringify(this.surveyData);
            this.editor.toolbox.getItemByName("radiogroup").json = {
                "type": "radiogroup",
                choices: []
            };

            this.editor.toolbox.getItemByName("checkbox").json = {
                "type": "checkbox",
                choices: []
            };

            this.editor.toolbox.getItemByName("dropdown").json = {
                "type": "dropdown",
                choices: []
            };
            this.editor.toolbox.getItemByName("imagepicker").json = {
                "type": "imagepicker",
                choices: []
            };
/*            var curStrings = this.editor
                .localization
                .getLocale("");*/
            let self = this;

            this.editor.survey.onAfterRenderSurvey.add(function () {
                CKEDITOR.editorConfig = function (config) {
                    config.language = "ar";

                };
            })
            this.editor.onGetPropertyReadOnly.add(function (sender, options) {
/*                console.log('readOnly');
                console.log(options);
                console.log(options.property);
                console.log(options.obj.getType())
                console.log(options.obj)*/
                if( options.obj.getType()=="imageitemvalue" && options.property.name == "value" ) {


                    options.readOnly = true;
                    return true;
                }
            });

            this.editor.onUploadFile.add(function(creator, options) {
                const config = {
                    headers: { 'content-type': 'multipart/form-data' }
                }
                console.log('options start');
                const getCircularReplacer = () => {
                    const seen = new WeakSet();
                    return (key, value) => {
                        if (typeof value === "object" && value !== null) {
                            if (seen.has(value)) {
                                return;
                            }
                            seen.add(value);
                        }
                        return value;
                    };
                };



                console.log(creator.selectedElement.uid)
                let formData = new FormData();
                formData.append('uid', creator.selectedElement.uid);
                options.files.forEach(function(file) {

                    formData.append(file.name, file);
                });

                axios.post('/survey/image/upload', formData, config)
                    .then(function (response) {
                        options.callback("success", "/upload/" + response.data[options.files[0].name]);
                    })
                    .catch(function (error) {
                       console.log(error);
                    });
                console.log('uploaddd')

            });
            this.editor.saveSurveyFunc = function () {
                axios.put('/survey/' + self.id, {json: JSON.parse(this.text)})
                    .then((response) => {
                        self.editor.text = JSON.stringify(response.data.data.json);
                        self.$root.snackbar = true;
                        self.$root.snackbarMsg = response.data.message;
                    })
                    .catch((error) => {
                        console.error(error.response);
                    })
            };
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style>
    .btn-primary {
        background-color: #1976d2 !important
    }
</style>
