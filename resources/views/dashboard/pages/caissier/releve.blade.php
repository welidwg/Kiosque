  @extends('dashboard/base')
  @section('title')
      Vos relevés
  @endsection
  @section('content')
      <div class="card shadow">
          <div class="card-header py-3 d-flex align-items-center justify-content-start">
              <p class="text-primary m-0 fw-bold fs-5"> Vos relevés

              </p>

              {{-- <a class="btn shadow-sm rounded-4 mx-3" href="{{ route('carburant.create') }}"><i class="fas fa-plus"></i>
                  Nouveau
                  produit</a>
              <a class="btn shadow-sm rounded-4 mx-3" href="{{ route('carburant.create') }}"><i class="fas fa-pen"></i>
                  Modifier
                  seuil</a>
              <a class="btn shadow-sm rounded-4 mx-3" href="{{ route('carburant.create') }}"><i class="fas fa-pen"></i>
                  Modifier
                  Jauge</a> --}}
          </div>
          <div class="card-body">

              <div class="table-responsive table mt-2" id="dataTable" role="grid" aria-describedby="dataTable_info">
                  <table class="table my-0" id="table_index_releve">
                      <thead>
                          <tr>
                              <th>Id</th>
                              <th>Date</th>
                              <th>Heure debut</th>
                              <th>Heure fin</th>
                              <th>Total Saisie</th>
                              <th>Total Rapport</th>
                              <th>Différence</th>
                              <th>Action</th>
                          </tr>
                      </thead>
                      <tbody>
                          @foreach ($releves as $releve)
                              <tr
                                  @if ($releve->diff != '0') style='background-color:rgba(255,0,0,0.3)' @else style='background-color:rgba(0,255,0,0.3)' @endif>
                                  <td>{{ $releve->id }}</td>
                                  <td>{{ $releve->date_systeme }}</td>
                                  <td>{{ $releve->heure_d }}</td>
                                  <td>{{ $releve->heure_f }}</td>
                                  <td>{{ $releve->totalSaisie }}</td>
                                  <td>{{ $releve->totalPdf }}</td>
                                  <td>{{ $releve->diff }}</td>
                                  <td>
                                      <div class="d-flex flex-row justify-content-evenly align-items-center">
                                          <form action="{{ route('releve.destroy', $releve->id) }}" method="POST">
                                              @method('DELETE')
                                              @csrf

                                              <a href="{{ route('releve.show', $releve->id) }}"><i
                                                      class="fas fa-eye text-dark"></i></a>
                                              {{-- <button type="submit" class="btn bg-transparent border-none"
                                                  id="delete_releve{{ $releve->id }}"
                                                  onclick="return confirm('Vois etes sur ?')"><i
                                                      class="fas fa-trash text-danger"></i></button> --}}
                                          </form>
                                      </div>
                                  </td>
                              </tr>
                          @endforeach


                      </tbody>

                  </table>
              </div>

          </div>
      </div>
      <script>
          $(document).ready(function() {
              $('#table_index_releve').DataTable({
                  order: []
              });
          });
      </script>
      {{-- <script src="{{ asset('/js/releve.js') }}"></script> --}}
  @endsection
