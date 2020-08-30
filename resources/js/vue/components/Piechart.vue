<template>
    <div class="pie-chart text-center p-md-3" >
        <canvas ref="canvas" class="mb-3 mt-3 p-0 col-11 col-md-4" ></canvas>
        <div ref="legend" class="text-left offset-1 col-10"></div>
    </div>
</template>

<script>
    export default {
        name: "Piechart",
        props: {
            size: {
                width: {
                    type:Number,
                    required: true
                },
                height: {
                    type: Number,
                    required: true
                }
            },
            data_obj: {
                type: Array,
                required: true
            },
            colors: {
                type: Array,
                required: true
            },
            doughnutHoleSize: {
                type: Number,
                required: true
            }
        },

        data() {
            return {
                canvas: null,
                legend: null,
                ctx: null
            }
        },

        mounted() {
          this.canvas = this.$refs.canvas;
          this.legend = this.$refs.legend;

          this.canvas.width  = this.size.width;
          this.canvas.height = this.size.height;

             // this.canvas.minWidth  = 200;
             // this.canvas.minHeight = 200;

          this.ctx = this.canvas.getContext('2d');
          this.draw();
        },

        watch: {
            data_obj() {
                this.draw();
            }
        },

        methods: {

            drawPieSlice(ctx, centerX, centerY, radius, startAngle, endAngle, color ){
                ctx.fillStyle = color;
                ctx.beginPath();
                ctx.moveTo(centerX,centerY);
                ctx.arc(centerX, centerY, radius, startAngle, endAngle);
                ctx.closePath();
                ctx.fill();
            },

            draw() {
                let total_value = 0;
                // let color_index = 0;

                for (let index in this.data_obj) {
                    let val = this.data_obj[index].value;
                    total_value += val;
                }

                let start_angle = 0;

                for (let index in this.data_obj) {
                    let val = this.data_obj[index].value;
                    let slice_angle = 2 * Math.PI * val / total_value;

                    this.drawPieSlice(
                        this.ctx,
                        this.canvas.width / 2,
                        this.canvas.height / 2,
                        Math.min(this.canvas.width / 2, this.canvas.height / 2),
                        start_angle,
                        start_angle + slice_angle,
                        this.data_obj[index].color
                        // this.colors[color_index % this.colors.length]
                    );

                    start_angle += slice_angle;
                    // color_index++;
                }

                //drawing a white circle over the chart
                //to create the doughnut chart
                if (this.doughnutHoleSize) {
                    this.drawPieSlice(
                        this.ctx,
                        this.canvas.width / 2,
                        this.canvas.height / 2,
                        this.doughnutHoleSize * Math.min(this.canvas.width / 2, this.canvas.height / 2),
                        0,
                        2 * Math.PI,
                        "transparent"
                    );
                }

                start_angle = 0;

                for (let index in this.data_obj){
                    let val = this.data_obj[index].value;
                    let slice_angle = 2 * Math.PI * val / total_value;
                    let pieRadius = Math.min(this.canvas.width/2,this.canvas.height/2);
                    let labelX = this.canvas.width/2 + (pieRadius / 2) * Math.cos(start_angle + slice_angle/2);
                    let labelY = this.canvas.height/2 + (pieRadius / 2) * Math.sin(start_angle + slice_angle/2);

                    if (this.doughnutHoleSize){
                        let offset = (pieRadius * this.doughnutHoleSize ) / 2;
                        labelX = this.canvas.width/2 + (offset + pieRadius / 2) * Math.cos(start_angle + slice_angle/2);
                        labelY = this.canvas.height/2 + (offset + pieRadius / 2) * Math.sin(start_angle + slice_angle/2);
                    }

                    let labelText = Math.round(100 * val / total_value);

                    if (labelText > 0) {
                        this.ctx.fillStyle = "white";
                        this.ctx.font = "bold 15px Arial";
                        this.ctx.fillText(labelText+"%",labelX,labelY);
                    }
                    start_angle += slice_angle;
                }

                if (this.legend) {
                    // color_index = 0;
                    let legendHTML = "";

                    for (let index in this.data_obj){
                        legendHTML += `
                            <div class="mb-2" style="font-size: 1.3em">
                                <p><span class="legend-item-ico" style='display:inline-block;width:25px;height:25px;vertical-align:middle;margin-bottom: 5px;background-color:${this.data_obj[index].color};border-radius: 5px;'></span>&nbsp;${this.data_obj[index].title} - ${this.data_obj[index].value} шт.</p>
                            </div>`;
                    }
                    legendHTML += `<hr><p>Всего заявок: ${total_value}</p>`;
                    this.legend.innerHTML = legendHTML;
                }

            }
        }
    }
</script>

<style scoped>

    .pie-chart {
        border: solid 1px #c5bebe;
        overflow: auto;
    }

</style>
