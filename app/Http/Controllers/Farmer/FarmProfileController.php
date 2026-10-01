<?php

namespace App\Http\Controllers\Farmer;

use App\Enums\Province;
use App\Http\Controllers\Controller;
use App\Http\Requests\Farmer\UpdateFarmProfileRequest;
use App\Services\CloudinaryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FarmProfileController extends Controller
{
    public function __construct(private readonly CloudinaryService $cloudinary) {}

    public function edit(Request $request): View
    {
        abort_if($request->user()->farmerProfile === null, 403);

        return view('farmer.profile.edit', [
            'farmer' => $request->user()->farmerProfile,
            'provinces' => Province::options(),
        ]);
    }

    public function update(UpdateFarmProfileRequest $request): RedirectResponse
    {
        $farmer = $request->user()->farmerProfile;
        $data = $request->validated();

        if ($request->hasFile('profile_image')) {
            if ($farmer->profile_image_public_id) {
                $this->cloudinary->delete($farmer->profile_image_public_id);
            }
            $uploaded = $this->cloudinary->uploadFarmProfileImage($request->file('profile_image'));
            $data['profile_image_url'] = $uploaded['url'];
            $data['profile_image_public_id'] = $uploaded['public_id'];
        }

        unset($data['profile_image']);
        $farmer->update($data);

        return redirect()
            ->route('farmer.profile.edit')
            ->with('status', 'Farm profile updated.');
    }
}
