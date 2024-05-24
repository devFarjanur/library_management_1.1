<?php

namespace App\Http\Controllers;

use App\Models\BorrowApproval;
use App\Models\BorrowRequest;
use App\Models\Category;
use App\Models\Feed;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;


use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\File;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;


use App\Models\User;
use App\Models\Book;

use Barryvdh\DomPDF\Facade\Pdf;


class AdminController extends Controller
{
    public function AdminDashboard()
    {
        $totalbooks = Book::count();
        $totalstudents = User::where('role', 'student')->where('approved', true)->count();
        $totalborrowed = BorrowApproval::where('status', 'approved')->count();
        $totalreturned = BorrowApproval::where('status', 'returned')->count();
        $students = User::where('role', 'student')->where('approved', true)->get();
        $payments = Payment::all();

        return view('admin.index', compact('totalbooks', 'totalstudents', 'students', 'totalborrowed', 'totalreturned', 'payments'));
    } // end method


    public function AdminAddPayment()
    {
        return view('admin.admin_add_payment');
    }


    public function AdminStorePayment(Request $request)
    {
        $validatedData = $request->validate([
            'paymentmethod' => 'required|string|max:255', // Update the field name
        ]);

        $payment = new Payment();
        $payment->payment_method_name = $request->paymentmethod; // Update the field name
        $payment->save();

        $notification = array(
            'message' => 'Payment Method added successfully.',
            'alert-type' => 'success'
        );

        return redirect()->route('admin.dashboard')->with($notification);

    }




    public function AdminRequestStudent()
    {
        // Fetch pending course teacher requests where approved is false
        $pendingStudentRequests = User::where('role', 'student')->where('approved', false)->get();

        return view('admin.Membership_student', compact('pendingStudentRequests'));
    }


    public function approveStudent($id)
    {
        Log::info("approveStudent method called with ID: $id");

        $student = User::find($id);

        if ($student) {
            Log::info("Student found: ", ['id' => $student->id, 'name' => $student->name]);

            $student->approved = true;
            $student->save();

            Log::info("Student approved successfully: ", ['id' => $student->id]);
        } else {
            Log::warning("Student not found with ID: $id");
        }

        // Redirect back with a success message
        $notification = [
            'message' => 'Student approved successfully.',
            'alert-type' => 'success'
        ];

        return redirect()->back()->with($notification);
    }


    public function rejectStudent($id)
    {
        log::info("rejectStudent method called with ID: $id");

        $user = User::findOrFail($id);

        if ($user) {
            Log::info("Student found for rejection: ", ['id' => $user->id, 'name' => $user->name]);

            $user->delete();

            Log::info("Student rejected and removed successfully: ", ['id' => $user->id]);
        } else {
            Log::warning("Student not found with ID: $id for rejection");
        }

        // Redirect back with a success message
        $notification = [
            'message' => 'Student rejected and removed successfully.',
            'alert-type' => 'success'
        ];

        return redirect()->back()->with($notification);
    }




    public function AdminLogout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }  // end method

    public function AdminLogin()
    {
        return view('admin.admin_login');
    }  // end method


    public function AdminProfile()
    {
        $id = Auth::user()->id;
        $profileData = User::find($id);
        return view('admin.admin_profile_view', compact('profileData'));
    }  // end method

    public function AdminProfileStore(Request $request)
    {
        $id = Auth::user()->id;
        $data = User::find($id);
        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;

        if ($request->file('photo')) {
            $file = $request->file('photo');
            @unlink(public_path('upload/admin_images/' . $data->photo));
            $filename = date('YmdHi') . $file->getClientOriginalName();
            $file->move(public_path('upload/admin_images'), $filename);
            $data->photo = $filename;
        }

        $data->save();

        $notification = array(
            'message' => 'Admin Profile Updated Successfully',
            'alter-type' => 'success'
        );


        return redirect()->back()->with($notification);
    } // end method



    public function AdminChangePassword()
    {
        $id = Auth::user()->id;
        $profileData = User::find($id);
        return view('admin.admin_change_password', compact('profileData'));
    } //  end method


    public function AdminCategories()
    {
        $categories = Category::all();
        return view('admin.book.admin_category', compact('categories'));
    }



    public function AdminCreateCategories()
    {
        return view('admin.book.admin_add_category');
    }


    public function AdminCategoriesStore(Request $request)
    {
        $validatedInput = $request->validate([
            'category_name' => 'required|string|max:255',
        ]);





        Category::create($validatedInput);

        $notification = array(
            'message' => 'Book Category added successfully.',
            'alert-type' => 'success'
        );

        return redirect()->route('admin.categories')->with($notification);

    }




    public function AdminEditCategory($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.book.admin_edit_category', compact('category'));
    }

    public function AdminUpdateCategory(Request $request, $id)
    {
        $validatedInput = $request->validate([
            'category_name' => 'required|string|max:255',
        ]);




        $category = Category::findOrFail($id);
        $category->update($validatedInput);

        return redirect()->route('admin.categories')->with([
            'message' => 'Book Category updated successfully.',
            'alert-type' => 'success'
        ]);
    }



    public function AdminDeleteCategory($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->route('admin.categories')->with([
            'message' => 'Book Category deleted successfully.',
            'alert-type' => 'success'
        ]);

    }




    public function AdminSearchBook(Request $request)
    {
        // Retrieve search criteria from the request
        $title = $request->input('title');
        $author = $request->input('author');
        $category = $request->input('category');
    
        // Retrieve all books initially
        $query = Book::query();
    
        // Apply search criteria if provided
        if ($title) {
            $query->where('title', 'like', '%' . $title . '%');
        }
    
        if ($author) {
            $query->where('author', 'like', '%' . $author . '%');
        }
    
        if ($category) {
            $query->whereHas('category', function ($q) use ($category) {
                $q->where('category_name', 'like', '%' . $category . '%');
            });
        }
    
        // Perform search based on criteria
        $searchResults = $query->with('category')->get();
    
        return view('admin.book.admin_search_book', compact('searchResults'));
    }







    // admin book

    public function AdminBook()
    {
        $categories = Category::all();
        $books = Book::all();
        return view('admin.book/admin_book', compact('books', 'categories'));
    } // end method


    public function AdminAddBook()
    {
        $categories = Category::all();
        return view('admin.book/admin_add_book', compact('categories'));
    } // end method

    public function adminBookStore(Request $request)
    {
        $validatedInput = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'edition' => 'required|string|max:255',
            'isbn' => 'required|string|max:255',
            'quantity' => 'required|string',
            'description' => 'required|string',
            'self_number' => 'required|string',
        ]);


        Book::create($validatedInput);

        return redirect()->route('admin.book')->with([
            'message' => 'Book Added Successfully',
            'alert-type' => 'success'
        ]);
    }



    public function editBook($id)
    {
        $categories = Category::all();
        $book = Book::findOrFail($id);
        return view('admin.book.admin_edit_book', compact('book', 'categories'));
    }

    public function updateBook(Request $request, $id)
    {
        $book = Book::findOrFail($id);
        $book->update($request->all());


        return redirect()->route('admin.book')->with([
            'message' => 'Book updated successfully.',
            'alert-type' => 'success'
        ]);

    }

    public function deleteBook($id)
    {
        $book = Book::findOrFail($id);
        $book->delete();

        return redirect()->route('admin.book')->with([
            'message' => 'Book deleted successfully.',
            'alert-type' => 'success'
        ]);

    }




    public function AdminEditBook($id)
    {
        $book = Book::findOrFail($id); // Find the product by ID

        return view('admin.book.admin_edit_book', compact('book'));
    }



    public function AdminBorrowRequest()
    {
        // Fetch all borrow requests for admins
        $borrowRequests = BorrowRequest::with('user')->get();

        return view('admin.book.admin_borrow_request', compact('borrowRequests'));
    }



    public function AdminApproveBorrowRequest(Request $request, BorrowRequest $borrowRequest)
    {
        // Check if the authenticated user has the 'admin' role
        if (auth()->user()->role !== 'admin') {
            return abort(403, 'Unauthorized');
        }

        // Find the book related to the borrow request
        $book = $borrowRequest->book;

        // Decrement the quantity of the book
        $book->decrement('quantity');

        // Create a new entry in borrow_approvals table
        BorrowApproval::create([
            'borrow_request_id' => $borrowRequest->id,
            'user_id' => $borrowRequest->user_id,
            'admin_id' => auth()->user()->id,
            'book_id' => $borrowRequest->book_id,
            'status' => 'approved'
        ]);

        // Update the status of the borrow request to approved
        $borrowRequest->update(['status' => 'approved']);

        return redirect()->back()->with('success', 'Borrow request approved successfully. Book quantity decremented.');
    }



    public function AdminRejectBorrowRequest(Request $request, BorrowRequest $borrowRequest)
    {
        // Check if the user is authorized to reject borrow requests


        if (auth()->user()->role !== 'admin') {
            return abort(403, 'Unauthorized');
        }

        // Create a new entry in borrow_approvals table
        BorrowApproval::create([
            'borrow_request_id' => $borrowRequest->id,
            'user_id' => $borrowRequest->user_id,
            'admin_id' => auth()->user()->id,
            'book_id' => $borrowRequest->book_id,
            'status' => 'rejected'
        ]);

        // Update the status of the borrow request to rejected
        $borrowRequest->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'Borrow request rejected successfully.');
    }



    public function AdminReturnedBook()
    {
        // Fetch the returned books
        $returnedBooks = BorrowApproval::where('status', 'returned')->with('user', 'book')->get();

        // Pass the returned books to the view
        return view('admin.book.admin_return_list', compact('returnedBooks'));
    }


    public function AdminReport()
    {
        $students = User::where('role', 'student')->get();

        $reportData = $students->map(function ($student) {
            $totalBooksBorrowed = $student->borrowRequests()->count();
            $totalBooksReturned = $student->borrowRequests()->whereHas('approvals', function ($query) {
                $query->where('status', 'returned');
            })->count();
            $totalBooksNotReturned = $totalBooksBorrowed - $totalBooksReturned;
            $totalFine = $student->borrowRequests()->with('approvals')->get()->sum(function ($borrowRequest) {
                return $borrowRequest->approvals->sum('fine');
            });
            $totalBooksPending = $student->borrowRequests()->where('status', 'pending')->count();
            $totalBooksApproved = $student->borrowRequests()->where('status', 'approved')->count();
            $totalBooksRejected = $student->borrowRequests()->where('status', 'rejected')->count();

            return [
                'student_name' => $student->name,
                'student_email' => $student->email,
                'total_books_borrowed' => $totalBooksBorrowed,
                'total_books_pending' => $totalBooksPending,
                'total_books_approved' => $totalBooksApproved,
                'total_books_rejected' => $totalBooksRejected,
                'total_books_returned' => $totalBooksReturned,
                'total_books_not_returned' => $totalBooksNotReturned,
                'total_fine' => $totalFine,
            ];
        });

        return view('admin.admin_report', compact('reportData'));
    }





    public function GeneratePdf()
    {
        // Fetch the report data similarly to AdminReport method
        $students = User::where('role', 'student')->get();

        $reportData = $students->map(function ($student) {
            $totalBooksBorrowed = $student->borrowRequests()->count();
            $totalBooksReturned = $student->borrowRequests()->whereHas('approvals', function ($query) {
                $query->where('status', 'returned');
            })->count();
            $totalBooksNotReturned = $totalBooksBorrowed - $totalBooksReturned;
            $totalFine = $student->borrowRequests()->with('approvals')->get()->sum(function ($borrowRequest) {
                return $borrowRequest->approvals->sum('fine');
            });
            $totalBooksPending = $student->borrowRequests()->where('status', 'pending')->count();
            $totalBooksApproved = $student->borrowRequests()->where('status', 'approved')->count();
            $totalBooksRejected = $student->borrowRequests()->where('status', 'rejected')->count();

            return [
                'student_name' => $student->name,
                'student_email' => $student->email,
                'total_books_borrowed' => $totalBooksBorrowed,
                'total_books_pending' => $totalBooksPending,
                'total_books_approved' => $totalBooksApproved,
                'total_books_rejected' => $totalBooksRejected,
                'total_books_returned' => $totalBooksReturned,
                'total_books_not_returned' => $totalBooksNotReturned,
                'total_fine' => $totalFine,
            ];
        });

        // Load the view and pass the data to it
        $pdf = Pdf::loadView('admin.admin_report_pdf', compact('reportData'));

        // Return the generated PDF
        return $pdf->download('admin_report.pdf');
    }

    public function index()
    {
        $feedbacks = Feed::with('user')->latest()->get();
        return view('admin.admin_feed', compact('feedbacks'));
    }






}
