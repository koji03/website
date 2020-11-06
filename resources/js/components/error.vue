<template>
    <div v-if="error" class="u-margin--error">
    <span v-for="item in error" :key="item.index" class="c-error" >
              {{ item }}
              <br v-if="item">
    </span>
  </div>
</template>
<script>
export default {
    props:{
        error:{
            required:false,
            type:Array
        },
    },
}
</script>