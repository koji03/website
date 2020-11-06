<template>
    <input  type="submit" :value=value class="c-btn c-form__group__submit c-btn--orange">
</template>

<script>
export default {
    props:{
        value:{
        type: String,
        required: true,
        }
    }
}
</script>