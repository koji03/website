<template>
    <div class="p-data__wrap">
            <CurrentAction/>
            <NumData/>
            <TotalNum/>
        </div>
</template>
<script>
import { OK } from '../../util'
import Loading from '../Loading.vue'
import CurrentAction from './LiveDataParts/CurrentAction'
import NumData from './LiveDataParts/NumData'
import TotalNum from './LiveDataParts/TotalNum'
export default {
    components:{
        CurrentAction,
        NumData,
        TotalNum,
        Loading
    },
}
</script>