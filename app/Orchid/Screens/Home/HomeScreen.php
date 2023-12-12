<?php

namespace App\Orchid\Screens\Home;

use App\Models\HomeContent;
use App\Orchid\Layouts\Home\HomeLayout;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;

class HomeScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'home' => HomeContent::first()
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Home Configuration';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make(__('Save'))
                ->icon('bs.pencil')
                ->method('save'),
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
            HomeLayout::class,
        ];
    }

    public function save(Request $request)
    {
        $data = $request->validate([
            'home.title' => 'required|string|max:255',
            'home.body' => 'required|string',
            'home.video' => 'required|string|url',
        ]);


        $home = HomeContent::first();
        if ($home) {
            $home->update($data['home']);
        } else {
            $home = HomeContent::create($data['home']);
        }

        Toast::success("Home was updated successfully.");

        return redirect()->back();
    }
}
