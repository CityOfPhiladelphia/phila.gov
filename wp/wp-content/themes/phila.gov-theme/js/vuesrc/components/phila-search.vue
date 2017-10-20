<template>
  <form
    @submit.prevent="onSubmit">
    <div class="search">
      <input class="search-field"
      type="text"
      :id="id"
      :name="name"
      :placeholder="placeholder"
      :ref="search-field">
      <input type="submit" value="submit" class="search-submit">
    </div>
  </form>
</template>

<script>
export default {
  name: 'search',
  props: ['submit'],
  methods: {
    onSubmit: function(event) {
        console.log(this)
    }
  }
}
</script>
