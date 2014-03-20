jQuery ->
  $('form').on 'click', '.remove_fields', (event) ->
    $(this).prev('input[type=hidden]').val('1')
    $(this).closest('fieldset').hide()
    event.preventDefault()

  $('form').on 'click', '.add_fields', (event) ->
    $(this).before($(this).data('fields'))
    event.preventDefault()
    ###
    insert new followup line before '.add_fields' button
    replace regexp of id with current time(creates unique id)
    $('input[id*=due_date]').datepicker()
    ###

    ###
    time = new Date().getTime()
    regexp = new RegExp($(this).data('id'), 'g')
    g(generate or global?) regexp based on task_id (i.e. data-id in helper method)
    ###

  $('form'). on 'change', 'select#number', (event) ->
    $('input[id$=due_date]').datepicker()

  $('form').on 'change', ('input[id*=tasks_attributes_0_description]'), (event) ->
    descriptions = $('input[id$=_description][id*=project_tasks_attributes][id*=subtasks_attributes]')
    $.each(descriptions, (index, element) ->
      index++
      text = $('input[id*=_tasks_attributes][id$=_description]').val().replace('IN','F' + index)
      console.log(text)
      $(element).val(text))

  $('form').on 'click', '.remove_mfields', (event) ->
    ###this removes the "task"###
    $(this).prev('input[type=hidden]').val('1')
    $(this).closest('div').hide()
    event.preventDefault()