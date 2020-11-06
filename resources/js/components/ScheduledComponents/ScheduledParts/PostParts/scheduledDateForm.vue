<template>
<div>
    <div class="p-scheduled__form--top">
        <div class="p-scheduled__form__wrap">
            <div class="p-scheduled__form__text">
                <span class="p-scheduled__form__text--pointer">月</span>
            </div>
            <div class="p-scheduled__form__select">
                <select name="" id="" v-model="date.month" class="p-scheduled__form__select__item">
                    <option v-for="n in 12" v-bind:key="n">{{ n }}</option>月
                </select>
            </div>
        </div>
        <div class="p-scheduled__form__wrap">
            <div class="p-scheduled__form__text">
                <span class="p-scheduled__form__text--pointer">日</span>
            </div>
            <div class="p-scheduled__form__select">
                <select name="" id="" v-model="date.day" class="p-scheduled__form__select__item">
                    <option v-for="n in lastday" v-bind:key="n">{{ n }}</option>
                </select>
            </div>
        </div>
        <div class="p-scheduled__form__wrap">
            <div class="p-scheduled__form__text">
                <span class="p-scheduled__form__text--pointer">年</span>
            </div>
            <div class="p-scheduled__form__select">
                <select name="" id="" v-model="date.year" class="p-scheduled__form__select__item">
                    <option v-for="n in 3" v-bind:key="n">{{ lastyear-1 + n }}</option>
                </select>
            </div>
        </div>
    </div>
        <div class="c-error u-margin--error--scheduled">{{error}}</div>

</div>
</template>
<script>
export default {
    data(){
        return{
            date:{
                day:'',
                year:'',
                month:'',
            },
            lastday:'',
            error:'',
            errorMessage:'過去の日時にツイートを予約することはできません。',
            lastyear:2030
        }
    },
    methods:{
        //それぞれの年と月から最大日数を調べる。
        monthday(){
            var lastday = new Array('', 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
            if ((this.date.year % 4 == 0 && this.date.year % 100 != 0) || this.date.year % 400 == 0){
                lastday[2] = 29;
            }
            this.lastday = lastday[this.date.month];
        },
        clearError(){
            this.error = ''
            this.$store.commit('modal/setDataErrorFlag',false)
        }
    },
    watch:{
        //年、月が入力されたときに日数が最大で何日かを調べて設定する。
        'date.year':function(){
            this.monthday()
        },
        'date.month':function(){
            this.monthday()
        },
        //設定した日付が過去の日付かを判定している。
        date:{
            handler: function () {
                this.clearError()
                var year1 = this.date.year;
                var month1 = this.date.month;
                var day1 = this.date.day;

                const date2 = new Date()
                var year2 = date2.getFullYear();
                var month2= date2.getMonth() + 1;
                var day2 = date2.getDate();
        
                if (year1 == year2) {
                    if (month1 == month2) {
                        if(day1 < day2){
                            this.error = this.errorMessage
                            this.$store.commit('modal/setDataErrorFlag',true)
                        }
                    }
                    else {
                        if(month1 < month2){
                            this.$store.commit('modal/setDataErrorFlag',true)
                            this.error = this.errorMessage
                        }
                    }
                } else {
                    if(year1 < year2){
                        this.$store.commit('modal/setDataErrorFlag',true)
                        this.error = this.errorMessage
                    }
                }
                this.$store.dispatch('modal/setDate',this.date)
            },
            deep: true
        }
    },
    created(){
        this.clearError()
        const year = new Date()
        this.lastyear = year.getFullYear()
        const settingDate =  this.$store.state.modal.settingDate
        if(settingDate){
            const date2 = new Date(settingDate);
            this.date.day = date2.getDate()
            this.date.month = date2.getMonth() + 1
            this.date.year = date2.getFullYear()
        }else{
            var date = new Date();
            date.setDate(date.getDate() + 1)
            this.date.day = date.getDate()
            this.date.month = date.getMonth() + 1
            this.date.year = date.getFullYear()
        }
    }
}
</script>