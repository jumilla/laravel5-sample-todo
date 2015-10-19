	{{-- 完了TODOリスト --}}
	<div id="todos-completed" class="todos-list row">
		<div class="col-sm-12 col-md-12">
			<h2>やったこと <span class="badge">{{ count($completedTodos) }}</span></h2>

			<table class="table table-striped">
				<thead>
					<tr>
						<th class="title col-sm-12 col-md-8">タイトル</th>
						<th class="completed_at col-sm-12 col-md-2">完了日</th>
						<th class="col-sm-12 col-md-2">&nbsp;</th>
					</tr>
				</thead>
				<tbody>
@if (count($completedTodos) > 0)
	@foreach ($completedTodos as $todo)
					<tr>
						<td id="todo-{{ $todo->id }}">
							<form method="POST" action="{!! route('todos.update', $todo->id) !!}">
								{!! csrf_field() !!}
								<input type="hidden" name="title" value="{{ $todo->title }}">
								<input type="hidden" name="status" value="{{ App\Todo::STATUS_INCOMPLETE }}">
								<button class="btn btn-link"><i class="glyphicon glyphicon-check"></i></button>
								{{ $todo->title }}
							</form>
						</td>
						<td>
							{{ date_string($todo->completed_at) }}
						</td>
						<td class="btn-group">
							<form method="POST" action="{!! route('todos.delete', $todo->id) !!}">
								{!! csrf_field() !!}
								<button class="btn btn-danger"><i class="glyphicon glyphicon-trash"></i></button>
							</form>
						</td>
					</tr>
	@endforeach
@else
					<tr>
						<td colspan="3">まだありません。</td>
					</tr>
@endif
				</tbody>
			</table>
		</div>
	</div>
