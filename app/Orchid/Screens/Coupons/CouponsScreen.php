<?php

namespace App\Orchid\Screens\Coupons;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class CouponsScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'coupons' => Coupon::latest()->get(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Discount Coupons';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Create Coupon')
                ->modal('createCoupon')
                ->method('create')
                ->icon('plus'),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [

            // table of coupons
            Layout::table('coupons', [
                TD::make('code', 'Coupon Code'),
                TD::make('number_of_uses', 'Number of Uses'),
                TD::make('number_of_used', 'Number of Used'),
                TD::make('discount_percentage', 'Discount Percentage'),
                TD::make('maximum_discount_amount', 'Maximum Discount Amount'),
                TD::make('valid_to', 'Valid To')->usingComponent(DateTimeSplit::class),
                TD::make('Actions')
                    ->alignRight()
                    ->cantHide()
                    ->render(function (Coupon $coupon) {
                        return Group::make([
                            ModalToggle::make('Update')
                                ->modal('updateCoupon')
                                ->method('update')
                                ->icon('pencil')
                                ->modalTitle('Update Coupon')
                                ->asyncParameters([
                                    'id' => $coupon->id
                                ]),
                            Button::make('Delete')
                                ->icon('trash')
                                ->confirm('After deleting, the skill will be gone forever.')
                                ->method('delete', ['coupon' => $coupon->id]),
                        ])->set('align', 'justify-content-end align-items-center')
                            ->autoWidth();
                    }),
            ]),

            // modal for creating coupons
            Layout::modal('createCoupon', [
                Layout::rows([
                    Input::make('coupon.code')
                        ->title('Code')
                        ->placeholder('Enter the code of the coupon')
                        ->required()
                        ->maxlength(10),
                    Input::make('coupon.number_of_uses')
                        ->title('Number of uses')
                        ->placeholder('Enter the number of uses of the coupon')
                        ->type('number')
                        ->min(0)
                        ->value(1)
                        ->required(),
                    Input::make('coupon.discount_percentage')
                        ->title('Discount percentage')
                        ->placeholder('Enter the discount percentage of the coupon')
                        ->type('number')
                        ->min(0)
                        ->max(99)
                        ->required(),
                    Input::make('coupon.maximum_discount_amount')
                        ->title('Maximum discount amount')
                        ->placeholder('Enter the maximum discount amount of the coupon')
                        ->type('number')
                        ->min(0)
                        ->nullable(),
                    DateTimer::make('coupon.valid_to')
                        ->title('Valid to')
                        ->placeholder('Enter the valid to of the coupon')
                        ->allowInput()
                        ->required(),
                ]),
            ])->title('Create Coupon'),

            // modal for editing coupons
            Layout::modal('updateCoupon', [
                Layout::rows([
                    Input::make('coupon.code')
                        ->title('Code')
                        ->disabled()
                        ->placeholder('Enter the code of the coupon')
                        ->maxlength(10),
                    Input::make('coupon.number_of_uses')
                        ->title('Number of uses')
                        ->placeholder('Enter the number of uses of the coupon')
                        ->type('number')
                        ->min(0)
                        ->required(),
                    Input::make('coupon.discount_percentage')
                        ->title('Discount percentage')
                        ->placeholder('Enter the discount percentage of the coupon')
                        ->type('number')
                        ->min(0)
                        ->max(99)
                        ->required(),
                    Input::make('coupon.maximum_discount_amount')
                        ->title('Maximum discount amount')
                        ->placeholder('Enter the maximum discount amount of the coupon')
                        ->type('number')
                        ->min(0)
                        ->nullable(),
                    DateTimer::make('coupon.valid_to')
                        ->title('Valid to')
                        ->allowInput()
                        ->placeholder('Enter the valid to of the coupon')
                        ->required(),
                ]),
            ])->async('asyncLoadCoupon')
                ->title('Edit Coupon'),
        ];
    }

    public function asyncLoadCoupon(string $id)
    {
        $coupon = Coupon::findOrFail($id);

        return [
            'coupon' => $coupon,
        ];
    }

    public function create(Request $request)
    {
        $data = $request->validate([
            'coupon.code' => 'required|unique:coupons,code|max:10|alpha_dash',
            'coupon.number_of_uses' => 'required|numeric|min:1',
            'coupon.discount_percentage' => 'required|numeric|min:0|max:99',
            'coupon.maximum_discount_amount' => 'nullable|numeric|min:0',
            'coupon.valid_to' => 'required|date_format:Y-m-d H:i:s',
        ]);

        Coupon::create($data['coupon']);

        Toast::success('Coupon created successfully!');

        return redirect()->route('platform.coupons');
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'coupon.number_of_uses' => 'required|numeric|min:1',
            'coupon.discount_percentage' => 'required|numeric|min:0|max:99',
            'coupon.maximum_discount_amount' => 'nullable|numeric|min:0',
            'coupon.valid_to' => 'required|date_format:Y-m-d H:i:s',
        ]);

        Coupon::find($request->id)->update($data['coupon']);

        Toast::success('Coupon updated successfully!');

        return redirect()->route('platform.coupons');
    }

    public function delete(Coupon $coupon)
    {
        $coupon->delete();

        Toast::success('Coupon deleted successfully!');

        return redirect()->route('platform.coupons');
    }
}
