<template>
  <form
    @submit.prevent="onSubmit">
    <div class="search">
      <input class="search-field"
      type="text"
      name="search"
      :id="id"
      :placeholder="placeholder"
      :aria-label="search">
      <input type="submit" value="submit" class="search-submit">
    </div>
  </form>
</template>

<script>
export default {
  name: 'search',
  data: function () {
    return {
      value: {
        default: null
      },
    }
  },
  props: {
    id: String,
    placeholder: String,
    ariaLabel: {
      type: String,
      default: 'Search'
    },
    onSubmit: {
      default: function (val) {
        this.$emit('input', val)
      }
    }
  },
  methods: {

  }
}
</script>
