<div id="filter_page" class="panel" title="Filter">
  <form id="filter_form" class="dialog" action="#item_list">
    <fieldset>
      <div>
        <div>
          <select id="filter_items_dropdown" class="filter_select filter_item_select">
          </select>
          <select id="filter_items_dropdown2_operator" class="filter_select filter_item_select">
            <option value="and">AND</option>
            <option value="or">OR</option>
          </select>
          <select id="filter_items_dropdown2" class="filter_select filter_item_select">
          </select>
        </div>
        <div>
          <select id="filter_items_completion_dropdown" class="filter_select">
            <option value="1">All</option>
            <option value="2">Complete</option>
            <option value="3" selected="selected">Not Complete</option>
          </select>
        </div>
        <div>
          <select id="filter_items_data_span" class="filter_select">
            <option value="1" selected="selected">Any Time</option>
            <option value="2">Today</option>
            <option value="3">Yesterday</option>
            <option value="4">Past Week</option>
            <option value="5">Past Month</option>
            <option value="6">Past Year</option>
            <option value="7">Past 5 Years</option>
          </select>
        </div>
        <div>
          <select id="filter_items_sort_field" class="filter_select">
            <option value="" selected="selected">Select Sort Field</option>
            <option value="completed">Completed</option>     
            <option value="count">Count</option>
            <option value="due_date">Due Date</option>
            <option value="name">Name</option>
            <option value="priority_id">Priority</option>
            <option value="updated">Updated</option>
          </select>
        </div>
        <div>
          <select id="filter_items_sort_direction" class="filter_select">
            <option value="" selected="selected">Select Sort Direction</option>
            <option value="ASC" selected="selected">Ascending</option>
            <option value="DESC">Descending</option>
          </select>
        </div>
        
        <input type="button" href="javascript:void();" value="Submit" class="button2 action_bar_item" id="filter_submit" style="display: inline;">
      </div>
    </fieldset>
  </form>
</div> 