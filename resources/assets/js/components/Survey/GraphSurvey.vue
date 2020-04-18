<template>
    <div>

        <div class="row">
            <div v-for="(questionnaire, q) in surveyQuestions" :key="q" class="col">
                <highcharts class="chart" :options="questionOption(questionnaire, q)" :updateArgs="updateArgs"></highcharts>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <table class="table  table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col">Company Participated</th>
                            <th scope="col">TSR Conducted Survey</th>
                            <th scope="col">Questions Answered</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ customers.length }}</td>
                            <td>{{ totalTsr.length }}</td>
                            <td>{{ surveyQuestions.length }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col">
                <table class="table  table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col">Rating</th>
                            <th scope="col">1</th>
                            <th scope="col">2</th>
                            <th scope="col">3</th>
                            <th scope="col">4</th>
                            <th scope="col">5</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Count</td>
                            <td>{{ ratings.filter(item => item.rating === 1).length }}</td>
                            <td>{{ ratings.filter(item => item.rating === 2).length }}</td>
                            <td>{{ ratings.filter(item => item.rating === 3).length }}</td>
                            <td>{{ ratings.filter(item => item.rating === 4).length }}</td>
                            <td>{{ ratings.filter(item => item.rating === 5).length }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <highcharts class="bar" :options="barOptions" :updateArgs="updateArgs"></highcharts>
            </div>
        </div>
    </div>
</template>

<script>
export default {

  props: {
      surveys: {
          type: Array,
          default: []
      },
      startDate: String,
      endDate: String,
      company: {
          type: Number,
          default: 0
      },
  },

  data () {
    return {
      questionnares: [],
      colorInputIsSupported: null,
      animationDuration: 1000,
      updateArgs: [true, true, {duration: 1000}],
      filteredSurveys: [],
      barOptions: {
          chart: {
            type: 'bar'
        },
        title: {
            text: 'Survey Ranking'
        },
        xAxis: {
            categories: this.tsrDistinctNames
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Rank Ratings'
            }
        },
        legend: {
            reversed: true
        },
        plotOptions: {
            series: {
                stacking: 'normal'
            }
        },
        series: [{
            name: '1',
            data: [5, 3, 4, 7, 2]
        }, {
            name: '2',
            data: [2, 2, 3, 2, 1]
        }, {
            name: '3',
            data: [2, 2, 3, 2, 1]
        }, {
            name: '4',
            data: [2, 2, 3, 2, 1]
        }, {
            name: '5',
            data: [3, 4, 4, 2, 5]
        }]
      },

      

 
    }
  },
  created () {
    let i = document.createElement('input')
    i.setAttribute('type', 'color');
    (i.type === 'color') ? this.colorInputIsSupported = true : this.colorInputIsSupported = false
  },

  computed: {

      ratings() {
          return this.surveys.flat().map(item => item.ranks.map(item => item.questions)).flat(2)
      },

      customers() {
          let customersFiltered =  this.surveys.flat().map(item => item.customer)
          return [...new Set(customersFiltered.map(item => item.id))]
      },

      totalTsr() {
          return [...new Set(this.surveys.flat().map(item => item.user_id))]
      },

      surveyQuestions() {
          return this.questionnares.survey_questionnaires
      },

      tsrDistinctNames() {
          let userFiltered =  this.surveys.flat().map(item => item.user)
          return [...new Set(userFiltered.map(item => item.name))]
      },


  },

  methods: {

      splitQuestion(key) {
        return this.ratings.filter((item, index) => { return (index % 2 === key)})
      },

      questionOption(questionnaire, key) {

        let questionSet = this.splitQuestion(key)

        return {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            credits: {
                enabled: false
            },
            title: {
            text: `Question ${key + 1}`
            },
            subtitle: {
                text: questionnaire.question
            },
            accessibility: {
                point: {
                    valueSuffix: '%'
                }
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                    }
                    // showInLegend: true
                }
            },
            series: [{
                name: 'Answers',
                colorByPoint: true,
                data: [{
                        name: 'Rating 1',
                        y: questionSet.filter(item => item.rating === 1).length,
                        sliced: true,
                        selected: true
                    }, {
                        name: 'Rating 2',
                        y: questionSet.filter(item => item.rating === 2).length
                    }, {
                        name: 'Rating 3',
                        y: questionSet.filter(item => item.rating === 3).length
                    }, {
                        name: 'Rating 4',
                        y: questionSet.filter(item => item.rating === 4).length
                    }, {
                        name: 'Rating 5',
                        y: questionSet.filter(item => item.rating === 5).length
                    }]
            }]
        }

      },

      getQuestionnaires() {


          axios.post(`/api/surveys/montly-questions`,{
              startDate: this.startDate,
              endDate: this.endDate,
              company: this.company,
          })
          .then(response => {
              this.questionnares = response.data
          })
      }
  },

  watch: {

    surveys(newValue) {
        this.filteredSurveys = newValue
        this.getQuestionnaires()
    },

    animationDuration (newValue) {
      this.updateArgs[2].duration = Number(newValue)
    }

  }
}
</script>

<style scoped>
input[type="color"]::-webkit-color-swatch-wrapper {
  padding: 0;
}
#colorPicker {
  border: 0;
  padding: 0;
  margin: 0;
  width: 30px;
  height: 30px;
}
.numberInput {
  width: 30px;
}
.highcharts-figure, .highcharts-data-table table {
    min-width: 320px; 
    max-width: 660px;
    margin: 1em auto;
}

.highcharts-data-table table {
	font-family: Verdana, sans-serif;
	border-collapse: collapse;
	border: 1px solid #EBEBEB;
	margin: 10px auto;
	text-align: center;
	width: 100%;
	max-width: 500px;
}
.highcharts-data-table caption {
    padding: 1em 0;
    font-size: 1.2em;
    color: #555;
}
.highcharts-data-table th {
	font-weight: 600;
    padding: 0.5em;
}
.highcharts-data-table td, .highcharts-data-table th, .highcharts-data-table caption {
    padding: 0.5em;
}
.highcharts-data-table thead tr, .highcharts-data-table tr:nth-child(even) {
    background: #f8f8f8;
}
.highcharts-data-table tr:hover {
    background: #f1f7ff;
}

</style>