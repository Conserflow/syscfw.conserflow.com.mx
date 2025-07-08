<template>
<bar-chart :chart-id="id" :data="barChartData" :options="barChartOptions" :height="200" ref="grafica" />
</template>

<script>
import BarChart from "../../Herramientas/Charts/BarChartAux.vue";

export default
{
    components:
    {
        BarChart
    },
    data()
    {
        return {
            id:"",
            barBackgroundColors: [],
            barChartData:
            {
                labels: [],
                datasets: [
                {
                    backgroundColor: [],
                    borderColor: [
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1,
                    data: [],
                }]
            },
            barChartOptions:
            {
                indexAxis: "y",
                tooltip:
                {
                    // enabled:true,
                },
                responsive: true,
                legend:
                {
                    display: false,
                    labels:
                    {},
                },
                title:
                {
                    display: true,
                    text: "ATENCIÓN MÉDICA"
                },
                scales:
                {
                    yAxes: [
                    {
                        display: true,
                        scaleLabel:
                        {
                            display: true,
                            labelString: "No. De Personal Atendido",
                            fontStyle: "bold"
                        },
                        ticks:
                        {
                            beginAtZero: true,
                            stepSize: 1
                        },
                    }],
                    xAxes: [
                    {
                        scaleLabel:
                        {
                            display: true,
                            labelString: "Causas De Atención",
                            fontStyle: "bold"
                        }
                    }]
                }
            }
        }
    },
    methods:
    {
        Mostrar(id,nombres, datos)
        {
            this.id=id;
            let colors = [];
            let borders = [];
            for (let i = 0; i < nombres.length; i++)
            {
                colors.push("#ffc116");
                borders.push("#cc980a");
            }
            this.barChartData.labels = nombres;
            this.barChartData.datasets[0].data = datos;
            this.barChartData.datasets[0].backgroundColor = colors;
            this.barChartData.datasets[0].borderColor = borders;
            this.$refs.grafica.Mostrar();
        }
    },
    mounted()
    {

    }
}
</script>
