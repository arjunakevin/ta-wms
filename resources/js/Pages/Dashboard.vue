<template>
    <div>
        <div class="row">
            <div class="col-md-6 mg-b-30" v-for="(chart, i) in charts" :key="`chart-${i}`">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mg-b-0">{{ chart.label }}</h6>
                    </div><!-- card-header -->
                    <div class="card-body pd-lg-25">
                        <div class="chart-seven"><canvas :id="chart.id"></canvas></div>
                    </div><!-- card-body -->
                    <div class="card-footer pd-20">
                        <div class="row">
                            <div class="col-6 mg-b-10" v-for="(data, j) in data[chart.data]" :key="`chart-data-${i}-${j}`">
                                <p class="tx-10 tx-uppercase tx-medium tx-color-03 tx-spacing-1 tx-nowrap mg-b-5">{{ data.label }}</p>
                                <div class="d-flex align-items-center">
                                <div class="wd-10 ht-10 rounded-circle mg-r-5" :style="`background: ${chart.bg[j]}`"></div>
                                    <h5 class="tx-normal tx-rubik mg-b-0">{{ data.count }} <small class="tx-color-04">{{ data.percentage }}%</small></h5>
                                </div>
                            </div>
                        </div><!-- row -->
                    </div><!-- card-footer -->
                </div><!-- card -->
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['data'],
    data() {
        return {
            charts: [
                {
                    id: 'inboundChart',
                    label: 'Inbound Delivery',
                    data: 'inbound_data',
                    bg: ['#f77eb9', '#fdbd88','#00cccc']
                },
                {
                    id: 'outboundChart',
                    label: 'Outbound Delivery',
                    data: 'outbound_data',
                    bg: ['#f77eb9', '#fdbd88','#00cccc']
                },
                {
                    id: 'grChart',
                    label: 'Good Receive',
                    data: 'gr_data',
                    bg: ['#f77eb9', '#fdbd88', '#55efc4', '#7ebcff', '#5b47fb', '#00cccc'],
                },
                {
                    id: 'doChart',
                    label: 'DeliveryOrder',
                    data: 'do_data',
                    bg: ['#f77eb9', '#fdbd88', '#55efc4', '#7ebcff', '#5b47fb', '#00cccc'],
                },
            ]
        };
    },
    methods: {
        initInboundDeliveryChart() {
            let data = {
                labels: this.data.inbound_data.map(i => i.label),
                datasets: [{
                    data: this.data.inbound_data.map(i => i.count),
                    backgroundColor: this.charts[0].bg
                }]
            };

            this.initChart('inboundChart', data);
        },
        initChart(id, data) {
            let context = document.getElementById(id);

            new Chart(context, {
                type: 'doughnut',
                data: data,
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    legend: {
                        display: false,
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    }
                }
            });
        }
    },
    mounted() {
        this.charts.map(chart => {
            let data = {
                labels: this.data[chart.data].map(i => i.label),
                datasets: [{
                    data: this.data[chart.data].map(i => i.count),
                    backgroundColor: chart.bg
                }]
            };

            this.initChart(chart.id, data)
        });
    }
}
</script>
