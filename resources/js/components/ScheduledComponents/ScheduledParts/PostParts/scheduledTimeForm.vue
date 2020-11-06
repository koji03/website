<template>
<div>
    <div class="p-scheduled__form--bottom">
        <div class="p-scheduled__form__wrap">
            <div class="p-scheduled__form__text">
                <span class="p-scheduled__form__text--pointer">時</span>
            </div>
            <div class="p-scheduled__form__select">
                <select name="" id="" v-model="hour" class="p-scheduled__form__select__item">
                    <option>0</option>
                    <option v-for="n in 23" v-bind:key="n">{{ n }}</option>
                </select>
            </div>
        </div>
        <div class="p-scheduled__form__wrap">
            <div class="p-scheduled__form__text">
                <span class="p-scheduled__form__text--pointer">分</span>
            </div>
            <div class="p-scheduled__form__select">
                <select name="" id="" v-model="minute" class="p-scheduled__form__select__item">
                    <option>0</option>
                    <option v-for="n in 5" v-bind:key="n">{{ n*10 }}</option>
                </select>
            </div>
        </div>
    </div>
    <div class="u-margin__scheduledTimeForm">現在日時から10分未満の場合は登録できません。</div>
    <div class="c-error u-margin--error--scheduled">{{error}}</div>
</div>
</template>
<script>
import { mapState } from 'vuex'
export default {
    data(){
        return{
            hour:'',
            minute:'',
            error:'',
            message:{
                reservation_error:'過去の日時にツイートを予約することはできません。'
            }
        }
    },
    methods:{
        clearError(){
            this.error = ''
            this.$store.commit('modal/setErrorFlag',false)
        },
         /**
         * 現在時刻より過去かを調べる。
         */
        errorCheck(){
            this.clearError()
            var date2 = new Date();
            var data1 = new Date(this.settingDate)
            const a = this.lowerThanDateOnly(data1,date2)
            if(a){
                var time1 = new Date(0,0,0,this.hour,this.minute);
                var time2 = new Date()
                const b = this.lowerThanTimeOnly(time1,time2)
                if(b){
                    this.error = this.message.reservation_error
                    this.$store.commit('modal/setErrorFlag',true)
                }
            }
            this.$store.dispatch('modal/setTime',{'hour':this.hour,'minute':this.minute})
        },
        lowerThanDateOnly(date1, date2) {
            var year1 = date1.getFullYear();
            var month1 = date1.getMonth() + 1;
            var day1 = date1.getDate();
    
            var year2 = date2.getFullYear();
            var month2= date2.getMonth() + 1;
            var day2 = date2.getDate();
    
            if (year1 == year2) {
                if (month1 == month2) {
                    return day1 == day2;
                }
            }
        },
        lowerThanTimeOnly(time1, time2) {
            var hours1 = time1.getHours();
            var minutes1 = time1.getMinutes();
            var seconds1 = time1.getSeconds();
    
            var hours2 = time2.getHours();
            var minutes2 = time2.getMinutes();
            var seconds2 = time2.getSeconds();
    
            if (hours1 == hours2) {
                if (minutes1 == minutes2) {
                    return seconds1 < seconds2;
                }
                else {
                    return minutes1 < minutes2;
                }
            } else {
                return hours1 < hours2;
            }
        },
    },
    watch:{
        hour:function(){
            this.errorCheck()
        },
        settingDate:function(){
            this.errorCheck()
        },
        minute:function(){
            this.errorCheck()
        }
    },
    computed:{
         ...mapState({
            settingDate: state => state.modal.settingDate,
        }),
    },
    created(){
        this.clearError()
        const settingTime =  this.$store.state.modal.settingTime
        if(settingTime){
            const date2 = new Date(settingTime);
            this.hour = date2.getHours()
            this.minute = date2.getMinutes()
        }else{
            this.hour = 0
            this.minute = 0
        }
    }
}
</script>