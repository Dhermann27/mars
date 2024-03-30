@extends('layouts.app')

@section('title')
    Housing Options
@endsection

@section('image')
    url('/images/housing.jpg')
@endsection

@section('content')
    <div class="container px-3 py-5 px-lg-4 py-lg-6 bg-grey mb-5">
        @component('components.layouts.blog', ['title' => 'Indiana University McNutt Dormitories'])

            <div class="mt-2">
                <p>We have multiple floors of rooms in two of the dorms in the McNutt Quadrangle. The rooms are mostly
                    doubles, with some singles. (View a tour of a double room in this complex <a
                        href="https://youtu.be/0V9ahh8OVew?si=trgCTtjuGRPjCO3v">here</a>. The beds are mostly lofted
                    (42in high) and take Twin XL sheets–but we will likely have at least a few rooms with at least one
                    bed at 20in height. The dorm rooms have drawers and wardrobes. Bathrooms are in the hall, with
                    public sinks and private toilets and showers. Each floor has a lounge. And yes, the dorms are
                    air-conditioned!</p>

                <p>Burt (Sr High, grades 10-12) and Meyer (jr high, grades 7-9) will be in the dorms, as will the Young
                    Adults. Dorm housing is also an option for anyone else who wants this affordable option!
                    PLEASE NOTE: There is a minimum age for staying in the dorms – youth will need to be at least 10
                    years old. All youth staying in the dorms will need to be in one of the MUUSA junior (Meyer) or
                    senior (Burt) high programs, or staying with a responsible adult.

                <p>We have a block of rooms available at Home2Suites, only 0.8 miles from UUCB. Its close proximity and
                    their willingness to give us a good rate at a distance that is walkable for many of us made them a
                    clear choice. In addition, they have a pool, a firepit, and free breakfast. Both king and double
                    queen suites with a pull-out couch and a kitchenette are available. These rooms will be available
                    until June 2024, after which they will be released to the general public.
                    The rate is $109 per night for up to 6 people per room depending on your choice of sleeping
                    arrangements. Lunch will be offered at UUCB, or you can opt for meals at the IU dining hall where
                    our residential youth programs (junior high through young adult) will be. (We will ask you to choose
                    at registration so everyone can plan with good numbers.) Ample parking should be available at each
                    location, and carpooling is encouraged. <a style="text-decoration: underline;" href="https://www.hilton.com/en/attend-my-event/bmgnwht-msa-be71305c-03ce-4ac2-8472-123c59ae3355/">Click here to reserve your room: Midwest UU Summer Assembly
                    (hilton.com)</a></p>

                <p>Bloomington has a variety of other hotels if this hotel does not fit your needs. Note that activities
                    for adults and children 6th grade and younger will be centered at UUCB, which is at the corner of
                    IN-45 and Fee Lane–you might want to find a hotel near here.</p>

            </div>

        @endcomponent
    </div>
@endsection
