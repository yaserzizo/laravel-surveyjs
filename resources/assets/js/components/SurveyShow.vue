<template>
    <div>
        <survey :survey="survey"></survey>
    </div>
</template>

<script>
    import * as SurveyVue from 'survey-vue';
    import * as SurveyPDF from "survey-pdf";
    import 'bootstrap/dist/css/bootstrap.css';
    import * as showdown from "showdown/dist/showdown.min";

    const Survey = SurveyVue.Survey;
    let onValueChangingProcessing = false;
    SurveyVue.StylesManager.applyTheme(SurveyConfig.theme)
    CKEDITOR.config.contentsLangDirection = 'rtl';
    CKEDITOR.config.contentsLanguage = 'ar';
    CKEDITOR.config.language = 'ar';
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
        components: {
            Survey
        },
        props: ['surveyData'],
        data () {
            return {
                survey: {}
            }
        },
        errorCaptured (err, vm, info) {
            this.error = `${err.stack}\n\nfound in ${info} of component`
            return false
        },
        created () {
            SurveyVue.Serializer.addProperty("page", "uid:number");
            SurveyVue.Serializer.addProperty("question", "uid:number");
            SurveyVue.Serializer.addProperty("question", "score:number");
            SurveyVue.Serializer.addProperty("question", "actualscore:number");
            SurveyVue.Serializer.addProperty("question", "maxitems:number");
            SurveyVue.Serializer.addProperty("itemvalue", "score:number");
            SurveyVue.Serializer.addProperty("survey", "total-score:number");
            SurveyVue.Serializer.addProperty("survey", "pass-score:number");

            //Survey.FunctionFactory.Instance.register(“age”, age);
            this.survey = new SurveyVue.Model(this.surveyData.json);
            this.survey
                .onAfterRenderQuestion
                .add(function (survey, options) {
                    if (options.question.inputType === "date" && options.question.isEmpty() && options.question.name == 'p.presentation_date') {
                        console.log('hjhggfff')
                        console.log(options.question)
                        options.question.value = new Date().toISOString().substring(0, 10);
                    }
                });
            this.survey.onAfterRenderSurvey.add(function(survey, options) {
                console.log('me');
                /*if (survey.pages[0].questions[0].name == 'currentDateTime') {
                    survey.pages[0].questions[0].value = new Date().toLocaleString();
                }*/
            });
            this.survey.pages.forEach(function(page) {
                page.questions.forEach(function (question) {

                    if (question.uid) {
                        question.valueName=question.uid;

                    }
                })
            });
            this.survey.locale="ar";
            this.survey.firstPageIsStarted = true;
            this.survey.showPrevButton = false;

            let self=this;
            this.survey.onValueChanged.add(function(sender, options) {
                console.log('chg')
                console.log(onValueChangingProcessing)
                if (onValueChangingProcessing) return;

                let q = options.question;
                if (q.name == 'p.birth_date') {
                    console.log('birthDate')
                    var birthDay = new Date(options.value);
                    if (birthDay) {
                        console.log(birthDay)

                        var ageDifMs = Date.now() -  birthDay.getTime();
                        var ageDate = new Date(ageDifMs); // miliseconds from epoch
                        let age =  Math.abs(ageDate.getUTCFullYear() - 1970);
                        onValueChangingProcessing=true
                        q.page.questions.forEach(function (qs) {
                            if (qs.name == 'p.age') {
                                qs.value= age;
                            }
                        });
                    }
                    onValueChangingProcessing = false;
                    return;
                }
                if (q && typeof q.choices !== 'undefined'){

                    if (q.name == 'p.id') {


                        let curpage = q.page;
                        let patient = null;
                        if (options.value <= 0 || options.value == null) {
                            curpage.questions.forEach(function (q) {
                                if (q.name == 'p.id') {
                                    return;
                                }
                                onValueChangingProcessing = true;
                                console.log('before')
                                q.value = null;
                                console.log('after')

                            })
                            console.log('fgfg')
                            onValueChangingProcessing = false;

                        } else {
                            axios
                                .get('/survey/patient/' + options.value)
                                .then(response => {
                                    patient = response.data

                                    curpage.questions.forEach(function (q) {
                                        if (q.name == 'p.id') {
                                            return;
                                        }
                                        onValueChangingProcessing = true;
                                        console.log('axios b')
                                        q.value = patient[q.name.split('.')[1]] ? patient[q.name.split('.')[1]] : null;
                                        console.log('axios a')

                                    })
                                    console.log('axios f')
                                    onValueChangingProcessing = false;
                                })
                        }
                        onValueChangingProcessing = false;
                        return;
                    }


                        onValueChangingProcessing = false;


                        let score = 0;
                        let calcscores=0;
                        console.log(options)
                    q.choices.forEach((c) => {

                        if (typeof c.score !== 'undefined' ) {
                            calcscores += c.score;
                        }
                    });
                    calcscores = (calcscores>0)?calcscores:1;
                        let percentscore = q.score>0?q.score/calcscores:1;
                        q.choices.forEach((c) => {

                            if (typeof c.score !== 'undefined' && q.value.toString().indexOf(c.itemValue) !== -1) {
                                score += (c.score * percentscore);
                            }
                        });
                        q.actualscore = score;
                        if (!q.maxitems || !q.prevValue || !options.value) {
                            q.prevValue = options.value;
                            onValueChangingProcessing = false;

                        }

                        else if (q.maxitems > 0 && Array.isArray(options.value)) {
                            onValueChangingProcessing = true;
                            if (options.value.length > q.maxitems) {

                                options.value.splice(-1, 1);

                                q.value = options.value;
                            }
                            onValueChangingProcessing = false;

                        }
                        onValueChangingProcessing = false;
                        q.prevValue = options.value;


                    }
                console.log('the end')
                onValueChangingProcessing = false;
            });
            this.survey.onIsAnswerCorrect.add(function (sender, options) {
                console.log('onIsAnswerCorrect fired')
                console.log('onIsAnswerCorrect - sender', sender)
                console.log('onIsAnswerCorrect - options', options.question.title)
                console.log('onIsAnswerCorrect - options', options.question.value)
                console.log('onIsAnswerCorrect - options', options.result)
            })
           // this.survey.startTimer();

            //Create showdown mardown converter
            var converter = new showdown.Converter();
           // this.survey.on

            this.survey.onCurrentPageChanged.add(function(sender, options) {
                console.log(options)
                console.log(options.oldCurrentPage.timeSpent)
               // this.survey.stopTimer();
               // this.survey.startTimer();
            });
            this.survey.onStarted.add(function (sender) {
                console.log('start fired');
               // this.survey.stopTimer();

                self.survey.startTimer();
                console.log(self.survey.timeSpent)
            })

            this.survey
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
        },
        methods: {
             renderTime(page,val){
                 return;
                if(!timeEl) return;
                var hours = Math.floor(val / 3600);
                var minutes = Math.floor((val - (hours*3600)) / 60);
                var seconds = Math.floor(val % 60);
                var timeText = hours + ":" + minutes + ":" + seconds;
               // timeEl.innerHTML = timeText;
            },
             calcScore(model) {
                var score = 0;
                var data = model.getPlainData();
                data.forEach((q) => {
                    if (typeof model.getQuestionByName(q.name).score !== 'undefined') {
                        score += model.getQuestionByName(q.name).score;
                    }
                    if (typeof model.getQuestionByName(q.name).choices !== 'undefined') {
                        model.getQuestionByName(q.name).choices.forEach((c) => {
                            if (typeof c.score !== 'undefined' && q.value.toString().indexOf(c.itemValue) !== -1) {
                                score += c.score;
                            }
                        });
                    }
                });
                return score;
             },
             timerCallback() {
                 this.survey.stopTimer();
                 var page = this.survey.currentPage;
                 console.log('page is');
                 console.log(this.survey.timeSpent);
                 console.log(page);
                 if(!page) {
                     this.survey.setValue('finalPg', this.survey.timeSpent);
                     this.survey.startTimer();
                     return;
                 };
                 var valueName = "pgNo" + this.survey.pages.indexOf(page);
                 console.log('valName is:'+ valueName);
                 var seconds = this.survey.timeSpent;// this.survey.getValue(valueName);
                // if(seconds == null) seconds = 0;
                // else seconds ++;
/*                 console.log(valueName + ';'+ seconds);
                 console.log(this.survey.getValue(valueName));
                 console.log(this.survey.getPlainData())*/
                 this.survey.setValue(valueName, seconds);

                 this.survey.startTimer();
               /* var page = this.survey.currentPage;
                if(!page) return;
                var valueName = "pgNo" + this.survey.pages.indexOf(page);
                var seconds = this.survey.getValue(valueName);
                if(seconds == null) seconds = 0;
                else seconds ++;
                console.log(valueName + ';'+ seconds);
                console.log(this.survey.getValue(valueName));
                console.log(this.survey.getPlainData())
                this.survey.setValue(valueName, seconds);*/
               // this.renderTime(valueName,seconds)
            }

        },
        mounted () {
            const self = this;
/*
            this.survey.onCurrentPageChanged.add(function(){
             //    self.timerCallback();
            });
*/

           // this.timerCallback();

/*            window.timerId = window.setInterval(function(){
                self.timerCallback();
            }, 1000);*/
            this.survey.onComplete.add((result) => {
                onValueChangingProcessing=true;
            //    self.timerCallback();
                //console.log(self.calcScore(result));
                console.log(result.data)
                result.stopTimer();
                let pgs = {};
                result.pages.forEach(function(page) {
                    if (page.name == 'patientinfo') {
                        let qs={};
                        qs["pgname"]=page.name;
                        page.questions.forEach(function (q) {
                            if (q.name.split(".").length > 1) {
                                let nm = q.name.split(".")[1];
                                qs[nm] = result.data[q.name];
                            }

                        });
                        console.log(page.name)
                        pgs[page.name] = qs;

                    }
                    else if (page.uid)
                    {
                        let g=[];
                        g.push({"time":page.timeSpent});
                       let qus = [];
                        page.questions.forEach(function (q) {
                            if (q.uid) {
                                qus.push({"id":q.uid,"name":q.name,"score":q.score,"actualscore":q.actualscore,"value":result.data[q.uid]});
                            }
                            else{
                                qus.push({"id":0,"name":q.name,"score":q.score,"actualscore":q.actualscore,"value":result.data[q.name]});
                            }
                        });
                        pgs[page.uid]={"pgname":page.name,"time":page.timeSpent,"answers":qus};
                        console.log(page.uid+":"+page.timeSpent)

                    }
                });
                result.setValue("pages", pgs);
                console.log('0000')
                console.log(result)

                //clearInterval(timerId);
               // survey.getPlainData()
                let url = `/survey/${this.surveyData.id}/result`
                axios.post(url, {json: result.data})
                    .then((response) => {

                        var options = {
                            commercial:true,
                            fontSize: 14,
                            margins: {
                                left: 10,
                                right: 10,
                                top: 10,
                                bot: 10
                            },
                            format: [210, 297]
                        };
                        console.log('1')
                        var surveyPDF = new SurveyPDF.SurveyPDF(this.surveyData.json, options);
                        console.log('2')
                       var converter = new showdown.Converter();
                        console.log('3')
                        /*var surv = this.survey;*/
                        surveyPDF.onTextMarkdown.add(function(surv, options) {
                            var str = converter.makeHtml(options.text);
                            str = str.substring(3);
                            str = str.substring(0, str.length - 4);
                            options.html = str;
                        });
                        console.log('4')
                        surveyPDF.data = this.survey.data;
                        console.log('5')
                        surveyPDF.mode = "display";
                        console.log('6')
                        surveyPDF.save('surveyResult.pdf');
                        console.log('7')
                    })
            })
        }
    }
</script>
