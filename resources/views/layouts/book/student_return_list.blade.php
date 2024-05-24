<x-app-layout>
    <div class="py-4">
        <div class="mx-auto sm:px-6 lg:px-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <div class="card-header text-xl mb-4">Return Book List</div>

                                <div class="card-body text-center">
                                    @if($borrowRequests->isNotEmpty())
                                    <table class="table mx-auto">
                                        <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th>Status</th>
                                                <th>Return Due Date</th>
                                                <th>Returned At</th>
                                                <th>Fine</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($borrowRequests as $borrowApproval)
                                                <tr>
                                                    <td>{{ $borrowApproval->borrowRequest->book->title }}</td>
                                                    <td>{{ ucfirst($borrowApproval->status) }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($borrowApproval->return_due_date)->format('Y-m-d H:i:s') }}</td>
                                                    <td>{{ $borrowApproval->returned_at ? \Carbon\Carbon::parse($borrowApproval->returned_at)->format('Y-m-d H:i:s') : 'Not returned yet' }}</td>
                                                    <td>
                                                        @if($borrowApproval->status === 'returned')
                                                            {{ $borrowApproval->fine }} Taka
                                                        @else
                                                            @if($borrowApproval->isLate())
                                                                {{ $borrowApproval->calculateFine() }} Taka ({{ $borrowApproval->calculateFine() / 100 }} seconds late)
                                                            @else
                                                                No fine
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($borrowApproval->status !== 'returned')
                                                            <form action="{{ route('student.return.book', $borrowApproval->id) }}" method="POST">
                                                                @csrf
                                                                <button type="submit" class="btn btn-danger">Return Book</button>
                                                            </form>
                                                        @elseif ($borrowApproval->status === 'returned' && $borrowApproval->fine > 0 && $borrowApproval->fine_status !== 'paid')
                                                            <form action="{{ route('borrow-approval.pay-fine', $borrowApproval->id) }}" method="POST" class="mt-2 d-flex flex-column align-items-center">
                                                                @csrf
                                                                <div class="d-flex flex-column flex-md-row mb-2 w-100">
                                                                    <select name="payment_method_id" class="form-control me-md-2 mb-2 mb-md-0" required>
                                                                        <option value="" selected disabled>Select Payment Method</option>
                                                                        @foreach($payments as $payment)
                                                                            <option value="{{ $payment->id }}">{{ $payment->payment_method_name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    <input type="text" name="TrxID" class="form-control me-md-2" placeholder="Transaction ID" required>
                                                                </div>
                                                                <button type="submit" class="btn btn-primary">Pay Fine</button>
                                                            </form>
                                                        @elseif ($borrowApproval->status === 'returned' && $borrowApproval->fine > 0 && $borrowApproval->fine_status === 'paid')
                                                            <button class="btn btn-success" disabled>Fine Paid</button>
                                                        @else
                                                            <button class="btn btn-secondary" disabled>Returned</button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @else
                                    <p>No borrowed books to show.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
